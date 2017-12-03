<?php

class AdvancedRecursiveDirectoryIterator
{
    const TOP_LEVEL = 0;

    private $topDir;
    private $structure = [];
    private $level = 0;

    public function __construct($topDir)
    {
        $this->topDir = $topDir;
    }

    private function createLevel($path, $level)
    {
        try {
            foreach (new DirectoryIterator($path) as $element) {
                if ($element->isDot()) {
                    continue;
                }
                if ($element->isLink()) {
                    continue;
                }
                if ($this->isHidden($element)) {
                    continue;
                }
                $this->structure[$level][] = $this->getElementData($element, $level);
                continue;
            }

            $subdirs = glob($path . '/*' , GLOB_ONLYDIR);
            $level++;

            foreach ($subdirs as $subdirPath) {
                $this->createLevel($subdirPath, $level);
            }
        } catch (Exception $e) {}
    }

    private function getElementData($element, $level)
    {
        $elementData = [];
        $elementData['level'] = $level;
        $elementData['type'] = $element->getType();
        $elementData['name'] = $element->getFilename();
        $elementData['basename'] = $element->isFile() ? $element->getBasename('.' . $element->getExtension()) : false;
        $elementData['extension'] = $element->isFile() ? $element->getExtension() : false;
        $elementData['path'] = $element->getPath();
        $elementData['size'] = $element->getSize();
        return $elementData;
    }

    private function isHidden($element)
    {
        if (strpos($element->getFilename(), '.') === 0) {
            return true;
        }
        return false;
    }

    public function getStructure()
    {
        if (!file_exists($this->topDir)) {
            return $this->structure;
        }
        $this->createLevel($this->topDir, self::TOP_LEVEL);
        return $this->structure;
    }
}

var_dump((new AdvancedRecursiveDirectoryIterator('/localhost/htdocs/python3'))->getStructure());