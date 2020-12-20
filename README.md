Yüklemek İçin
`composer require navari/spider-vo`

Class başlatmak için 

`$spider = new Spider((new Client()));`

yazarak kullandığınız http paketini gönderebilirsiniz. 

Kullanmak için gerekli methodlar

`$spider->setAgencyId(example_agency_id);`

olarak **ajans id si** göndermeniz gerekmektedir. yoksa getirmeyecektir.

Tüm sayfalarda bulunan ilanları çekmek için 

`$spider->getAllPage();`

size array döndürecektir her bir array içeriği örnek response aşağıdaki gibidir : 

` Array
(
[name] => DS-DS3- 1.6 BlueHDi S&S - 100 So Chic
[link] => https://www.spider-vo.net/ds-ds3--16-bluehdi-ss---100-so-chic,svos62.html?vid=1157818
[image] => https://www.spider-vo.net/ds-ds3--16-bluehdi-ss---100-so-chic,fm-svosites!fd001!78567038!n.jpg
[brand] => DS DS3
[model] =>  1.6 BlueHDi S&S - 100 So Chic
[description] => 2016- | 134 250 km | Diesel
[price] => 9 990 €
)
`

Tek bir sayfayı döndürmek için ise

`$spider->get(sayfa_numarası);`

olarak işlem yapabilirsiniz. Tüm methodlar aynı array yapısını döndürür.