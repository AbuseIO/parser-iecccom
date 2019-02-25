<?php

namespace AbuseIO\Parsers;

use AbuseIO\Models\Incident;

class Iecccom extends ParserBase
{

    /**
     * Parse attachments
     * @return array    Returns array with failed or success data
     *                  (See parser-common/src/Parser.php) for more info.
     */
    public function parse()
    {
        if ($this->arfMail === false) {
            $this->warningCount++;
            return $this->success();\
        }
        $this->feedName = 'default';

        // If feed is known and enabled, validate data and save report
        $kae = $this->isKnownFeed() && $this->isEnabledFeed();
        if (!$kae) {
            return $this->success();
        }
        $matched = preg_match_all('/([\w\-]+): (.*)[ ]*\r?\n/', $this->arfMail['report'], $matches);
        if (!$matched) {
            $this->warningCount++;
            return $this->success();
        }
        $report = array_combine($matches[1], $matches[2]);
        // Sanity check
        if (($this->hasRequiredFields($report) !== true) || ($report['Feedback-Type'] !== 'abuse')) {
            return $this->success();
        }
        // incident has all requirements met, filter and add!
        $report = $this->applyFilters($report);

        $report['evidence'] = $this->arfMail['evidence'];

        $incident = new Incident();
        $incident->source      = config("{$this->configBase}.parser.name");
        $incident->source_id   = false;
        $incident->ip          = $report['Source-IP'];
        $incident->domain      = false;
        $incident->class       = config("{$this->configBase}.feeds.{$this->feedName}.class");
        $incident->type        = config("{$this->configBase}.feeds.{$this->feedName}.type");
        $incident->timestamp   = strtotime($report['Received-Date']);
        $incident->information = json_encode($report);

        $this->incidents[] = $incident;

        return $this->success();
    }
}
