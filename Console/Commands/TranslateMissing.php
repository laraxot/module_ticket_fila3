<?php

declare(strict_types=1);

namespace Modules\Ticket\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Safe\json_decode;
use function Safe\json_encode;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Webmozart\Assert\Assert;

class TranslateMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trans:missing {base}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a JSON translation for missing keys, based on a locale file "base".';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $base = $this->argument('base');
        Assert::isArray($locales = config('system.locales.list'));
        Assert::isArray($baseTranslations = json_decode((string) File::get(lang_path($base.'.json')), true, 512, JSON_THROW_ON_ERROR));
        $this->info('Found '.(is_countable($locales) ? \count($locales) : 0).' locales. Performing, please wait...');
        $bar = $this->getOutput()->createProgressBar(is_countable($locales) ? \count($locales) : 0);
        $bar->start();
        foreach ($locales as $locale => $name) {
            if ($locale !== $base && $locale !== config('app.fallback_locale')) {
                $filePath = lang_path($locale.'.json');
                if (File::exists($filePath)) {
                    Assert::isArray($localeTranslations = json_decode((string) File::get(lang_path($locale.'.json')), true, 512, JSON_THROW_ON_ERROR));
                    $translator = new GoogleTranslate($locale);
                    $translator->setSource('en');
                    $newLocaleTranslations = [];
                    foreach ($baseTranslations as $kbt => $baseTranslation) {
                        if (! \array_key_exists($kbt, $localeTranslations)) {
                            $newLocaleTranslations[$kbt] = $translator->translate($kbt);
                        } else {
                            $newLocaleTranslations[$kbt] = $localeTranslations[$kbt];
                        }
                    }

                    File::put($filePath, json_encode($newLocaleTranslations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                }
            }

            $bar->advance();
        }

        $bar->finish();

        return Command::SUCCESS;
    }
}
