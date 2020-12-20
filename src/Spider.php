<?php


namespace Navari\Spider;


use Navari\Spider\Http\Request;
use Psr\Http\Client\ClientInterface;
use stdClass;

class Spider
{
    private $client;
    private $agencyId;
    private $sort = 'date';
    private $sortType = 'D';
    private $category = '';
    private $brand = '';
    private $model = '';
    private $fuel = '';
    private $priceMin = 0;
    private $priceMax = 105000000;
    private $yearMin = 1994;
    private $yearMax = 2019;
    private $oddMax = 999999;
    private $totalPage;
    const MAIN_URL = 'https://www.spider-vo.net/xhr_ws.php?xhr_func=svosites_aj002';

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        Request::setHttpClient($client);
    }

    /**
     * @param ClientInterface $client
     * @return Spider
     */
    public function setClient(ClientInterface $client): Spider
    {
        $this->client = $client;
        Request::setHttpClient($client);
        return $this;
    }
    /**
     * @param mixed $agencyId
     * @return Spider
     */
    public function setAgencyId($agencyId): Spider
    {
        $this->agencyId = $agencyId;
        return $this;
    }

    /**
     * @param mixed $brand
     * @return Spider
     */
    public function setBrand($brand): Spider
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @param mixed $category
     * @return Spider
     */
    public function setCategory($category): Spider
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @param mixed $fuel
     * @return Spider
     */
    public function setFuel($fuel): Spider
    {
        $this->fuel = $fuel;
        return $this;
    }

    /**
     * @param mixed $model
     * @return Spider
     */
    public function setModel($model): Spider
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param int $oddMax
     * @return Spider
     */
    public function setOddMax(int $oddMax): Spider
    {
        $this->oddMax = $oddMax;
        return $this;
    }

    /**
     * @param int $priceMax
     * @return Spider
     */
    public function setPriceMax(int $priceMax): Spider
    {
        $this->priceMax = $priceMax;
        return $this;
    }

    /**
     * @param int $priceMin
     * @return Spider
     */
    public function setPriceMin(int $priceMin): Spider
    {
        $this->priceMin = $priceMin;
        return $this;
    }

    /**
     * @param string $sort
     * @return Spider
     */
    public function setSort(string $sort): Spider
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @param string $sortType
     * @return Spider
     */
    public function setSortType(string $sortType): Spider
    {
        $this->sortType = $sortType;
        return $this;
    }

    /**
     * @param int $yearMax
     * @return Spider
     */
    public function setYearMax(int $yearMax): Spider
    {
        $this->yearMax = $yearMax;
        return $this;
    }

    /**
     * @param int $yearMin
     * @return Spider
     */
    public function setYearMin(int $yearMin): Spider
    {
        $this->yearMin = $yearMin;
        return $this;
    }

    /**
     * @param stdClass|string $rawError
     *
     * @return string
     */
    private static function getErrorBody($rawError): string
    {
        if (is_string($rawError)) {
            return $rawError;
        }
        if (is_object($rawError)) {
            $str = '';
            foreach ($rawError as $key => $value) {
                $str .= ' ' . $key . ' => ' . $value . ';';
            }
            return $str;
        } else {
            return 'Unknown body format';
        }

    }


    /**
     * @param $page
     * @return Http\Response
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function buildQuery($page): Http\Response
    {
        $xhr_args = [
            $this->agencyId, 0, $page, $this->sort, $this->sortType, $this->category, $this->brand, $this->model, $this->fuel, $this->priceMin, $this->priceMax, $this->yearMin, $this->yearMax, $this->oddMax, ''
        ];
        $url = '';
        foreach($xhr_args as $arg){
            $url .= '&xhr_args[]='.$arg;
        }
        return Request::get(self::MAIN_URL. $url);
    }

    public function get($page): array
    {
        $response = $this->matchResponse($this->buildQuery($page));
        return $response;
    }

    private function getTotalPageCount($response)
    {
        preg_match_all('@<li(.*?)><span>(.*?)</span></li>@si',$response->raw_body,$pages);
        $this->totalPage = count($pages[0]);
    }

    private function matchResponse($response): array
    {
        $cars = [];
        preg_match_all('@<a href="(.*?)" title="(.*?)" class="image" >                  <img style="width:100%;max-width:100%;height:auto;" src="(.*?)" alt="(.*?) " />                  <!--(.*?)--></a><!--<a class="bouton_detail" href="(.*?)" id="(.*?)">(.*?)</a>-->                            <h3><span class="marquemodele">(.*?)</span><span class="version">(.*?)</span></h3>                <!--<div class="reference">ref. : (.*?)</div>--><div class="clearfix infos-car">              <div>(.*?)</div>              <div><div class="prix">(.*?)</div></div>            </div@si',$response->raw_body, $return);
        for($i = 0; $i < count($return[0]); $i++){
            $car = [
                'name' => $return[2][$i],
                'link' => $return[1][$i],
                'image' => $return[5][$i],
                'brand' => $return[9][$i],
                'model' => $return[10][$i],
                'description' => $return[12][$i],
                'price' => $return[13][$i]
            ];
            $cars[] = $car;
        }
        return $cars;
    }

    public function getAllPage(): array
    {
        $response = $this->buildQuery(1);
        $this->getTotalPageCount($response);
        $cars = [];
        for($i = 0; $i < $this->totalPage; $i++){
            $r = $this->matchResponse($this->buildQuery($i));
            $cars = array_merge($cars, $r);
//            $cars[] = $r;
        }
        return $cars;
    }
}