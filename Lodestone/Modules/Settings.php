<?php
namespace Lodestone\Modules;

trait Settings
{
    /**
     * Set optional useragent
     *
     * @test .
     * @param string $useragent
     * @return this
     */
    public function setUseragent(string $useragent = "")
    {
        $this->useragent = $useragent;
        return $this;
    }
    
    /**
     * Set optional langauge
     *
     * @test .
     * @param string $useragent
     * @return this
     */
    public function setLanguage(string $language = "")
    {
        if (!in_array($language, self::langallowed)) {
            $language = "na";
        }
        if (in_array($language, ['jp', 'ja'])) {$language = 'jp';}
        $this->lang = $language;
        $this->language = 'https://'.$language;
        return $this;
    }
    
    /**
     * Set benchmarking on or off
     *
     * @test .
     * @param string $useragent
     * @return this
     */
    public function setBenchmark($bench = false)
    {
        $this->benchmark = (bool)$bench;
        return $this;
    }
}
?>