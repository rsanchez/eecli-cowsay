<?php

namespace eecli\Cowsay;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CowsayCommand extends Command
{
    protected $eyeModes = array(
        'borg' => '==',
        'dead' => 'xx',
        'greedy' => '$$',
        'paranoia' => '@@',
        'stoned' => '**',
        'tired' => '--',
        'wired' => 'OO',
        'youthful' => '..',
    );

    protected $tongueModes = array(
        'stoned' => 'U ',
        'dead' => 'U ',
    );

    /**
     * {@inheritdoc}
     */
    protected $name = 'cowsay';

    /**
     * {@inheritdoc}
     */
    protected $description = 'The cow says...';

    /**
     * {@inheritdoc}
     */
    protected function getArguments()
    {
        return array(
            array(
                'message', // name
                InputArgument::IS_ARRAY, // mode
                'What does the cow say?', // description
                null, // default value
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions()
    {
        return array(
            array(
                'eye_string',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Change the cow\'s eyes.',
            ),
            array(
                'tongue_string',
                'T',
                InputOption::VALUE_OPTIONAL,
                'Change the cow\'s tongue.',
            ),
            array(
                'wordwrap',
                'W',
                InputOption::VALUE_OPTIONAL,
                'How many characters to use per line.',
            ),
            array(
                'borg',
                'b',
                InputOption::VALUE_NONE,
                'Borg mode',
            ),
            array(
                'dead',
                'd',
                InputOption::VALUE_NONE,
                'Dead mode',
            ),
            array(
                'greedy',
                'g',
                InputOption::VALUE_NONE,
                'Greedy mode',
            ),
            array(
                'paranoia',
                'p',
                InputOption::VALUE_NONE,
                'Paranoia mode',
            ),
            array(
                'stoned',
                's',
                InputOption::VALUE_NONE,
                'Stoned mode',
            ),
            array(
                'tired',
                't',
                InputOption::VALUE_NONE,
                'Tired mode',
            ),
            array(
                'wired',
                'w',
                InputOption::VALUE_NONE,
                'Wired mode',
            ),
            array(
                'youthful',
                'y',
                InputOption::VALUE_NONE,
                'Youthful mode',
            ),
        );
    }

    protected function getTemplate()
    {
        return <<<EOT
 {{BORDER}}
{{SPEECH_BUBBLE}}
 {{BORDER}}
        \   ^__^
         \  ({{EYES}})\_______
            (__)\       )\/\
             {{TONGUE}} ||----w |
                ||     ||
EOT;
    }

    protected function getEyes()
    {
        $eyes = $this->option('eye_string');

        if ($eyes) {
            if (strlen($eyes) < 2) {
                throw new \RuntimeException('eye_string requires two characters.');
            }

            return substr($eyes, 0, 2);
        }

        if ($this->option('borg')) {
            return $this->eyeModes['borg'];
        }

        if ($this->option('dead')) {
            return $this->eyeModes['dead'];
        }

        if ($this->option('greedy')) {
            return $this->eyeModes['greedy'];
        }

        if ($this->option('paranoia')) {
            return $this->eyeModes['paranoia'];
        }

        if ($this->option('stoned')) {
            return $this->eyeModes['stoned'];
        }

        if ($this->option('tired')) {
            return $this->eyeModes['tired'];
        }

        if ($this->option('wired')) {
            return $this->eyeModes['wired'];
        }

        if ($this->option('youthful')) {
            return $this->eyeModes['youthful'];
        }

        return 'oo';
    }

    protected function getTongue()
    {
        $tongue = $this->option('tongue_string');

        if ($tongue) {
            if (strlen($tongue) < 2) {
                throw new \RuntimeException('tongue_string requires two characters.');
            }

            return substr($tongue, 0, 2);
        }

        if ($this->option('stoned')) {
            return $this->tongueModes['stoned'];
        }

        if ($this->option('dead')) {
            return $this->tongueModes['dead'];
        }

        return '  ';
    }

    protected function getMessage()
    {
        // get all arguments as one message
        $message = implode(' ', $this->argument('message'));

        if ($message === '--') {
            $message = file_get_contents('php://input');
        }

        if ($message === '') {
            $message = $this->getRandomMessage();
        }

        return $message;
    }

    protected function getMessageLines()
    {
        $message = $this->getMessage();

        $wrapLength = $this->option('wordwrap') ?: 40;

        // wrap the message to max chars
        $message = wordwrap($message, $wrapLength - 2);

        // split into array of lines
        return explode("\n", $message);
    }

    protected function getMaxLineLength(array $lines)
    {
        $lineLength = 0;

        // find the longest line
        foreach ($lines as $line) {
            $currentLineLength = strlen($line);

            if ($currentLineLength > $lineLength) {
                $lineLength = $currentLineLength;
            }
        }

        return $lineLength;
    }

    protected function getBorder($lineLength)
    {
        return str_repeat('-', $lineLength + 2);
    }

    protected function getSpeechBubble(array $lines, $lineLength)
    {
        $text = '';

        $numberOfLines = count($lines);

        $firstLine = str_pad(array_shift($lines), $lineLength);

        if ($numberOfLines === 1) {
            $text = "< {$firstLine} >";
        } else {
            $lastLine = str_pad(array_pop($lines), $lineLength);

            $text = "/ {$firstLine} \\\n";

            foreach ($lines as $line) {
                $line = str_pad($line, $lineLength);
                $text .= "| {$line} |\n";
            }

            $text .= "\\ {$lastLine} /";
        }

        return $text;
    }

    protected function getRandomMessage()
    {
        switch (mt_rand(1, 5)) {
            case 1:
                return sprintf('You have %d entries!', ee()->db->count_all_results('channel_titles'));
            case 2:
                return sprintf('You have %d members!', ee()->db->count_all_results('members'));
            case 3:
                $devLogCount = ee()->db->count_all_results('developer_log');
                return $devLogCount
                    ? sprintf('Uh oh. You have %d item in your developer log.', ee()->db->count_all_results('developer_log'))
                    : 'Good. Your developer log is empty.';
            case 4:
                return sprintf('Your site name is "%s".', ee()->config->item('site_label'));
            case 5:
                return sprintf('The latest entry is called "%s".', ee()->db->select('title')->limit(1)->order_by('entry_id', 'desc')->get('channel_titles')->row('title'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $lines = $this->getMessageLines();
        $lineLength = $this->getMaxLineLength($lines);

        $this->output->writeln(str_replace(
            array(
                '{{BORDER}}',
                '{{SPEECH_BUBBLE}}',
                '{{EYES}}',
                '{{TONGUE}}',
            ),
            array(
                $this->getBorder($lineLength),
                $this->getSpeechBubble($lines, $lineLength),
                $this->getEyes(),
                $this->getTongue(),
            ),
            $this->getTemplate()
        ));
    }
}
