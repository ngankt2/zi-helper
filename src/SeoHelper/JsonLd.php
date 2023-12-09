<?php

namespace ZiBase\SeoHelper;


use Illuminate\Config\Repository as Config;

class JsonLd
{
    /**
     * @var array
     */
    protected $values = [];
    /**
     * @var string
     */
    protected $type = '';
    /**
     * @var string
     */
    protected $title = '';
    /**
     * @var string
     */
    protected $description = '';
    /**
     * @var string|null|bool
     */
    protected $url = false;
    /**
     * @var array
     */
    protected $images = [];

    private static $singleInstance;

    public static function &getInstance(){
        if(!self::$singleInstance){
            self::$singleInstance = new self(app('config')->get('zi.seo.json-ld.defaults'));
        }
        return self::$singleInstance;
    }


    public function __construct($defaults = [])
    {
        if (key_exists('title', $defaults)) {
            $this->setTitle($defaults['title']);
            unset($defaults['title']);
        }

        if (key_exists('description', $defaults)) {
            $this->setDescription($defaults['description']);
            unset($defaults['description']);
        }

        if (key_exists('type', $defaults)) {
            $this->setType($defaults['type']);
            unset($defaults['type']);
        }

        if (key_exists('url', $defaults)) {
            $this->setUrl($defaults['url']);
            unset($defaults['url']);
        }

        if (key_exists('images', $defaults)) {
            $this->setImages($defaults['images']);
            unset($defaults['images']);
        }

        $this->values = $defaults;
    }


    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->values)
            && empty($this->type)
            && empty($this->title)
            && empty($this->description)
            && empty($url)
            && empty($this->images);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($minify = false)
    {
        $generated = array_merge(
            [
                '@context' => 'https://schema.org',
            ],
            $this->convertToArray()
        );

        return '<script type="application/ld+json">' . json_encode($generated, JSON_UNESCAPED_UNICODE) . '</script>';
    }

    /**
     * @return string[]|string[][]
     */
    public function convertToArray()
    {
        $generated = [];

        if (!empty($this->type)) {
            $generated['@type'] = $this->type;
        }

        if (!empty($this->title)) {
            $generated['name'] = $this->title;
        }

        if (!empty($this->description)) {
            $generated['description'] = $this->description;
        }

        if ($this->url !== false) {
            $generated['url'] = $this->url ?? app('url')->full();
        }

        if (!empty($this->images)) {
            $generated['image'] = count($this->images) === 1 ? reset($this->images) : $this->images;
        }

        return self::convertSelfObjectInArray(array_merge($generated, $this->values));
    }

    /**
     * @param mixed[] $values
     *
     * @return string[]|string[][]
     */
    private static function convertSelfObjectInArray($values)
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = self::convertSelfObjectInArray($value);

                continue;
            }

            if ($value instanceof self) {
                $values[$key] = $value->convertToArray();
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function addValue($key, $value)
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addValues($values)
    {
        foreach ($values as $key => $value) {
            $this->addValue($key, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSite($site)
    {
        $this->url = $site;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     *{@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setImages($images)
    {
        $this->images = [];

        return $this->addImage($images);
    }

    /**
     * {@inheritdoc}
     */
    public function addImage($image)
    {
        if (is_array($image)) {
            $this->images = array_merge($this->images, $image);
        } elseif (is_string($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image)
    {
        $this->images = [$image];

        return $this;
    }
}
