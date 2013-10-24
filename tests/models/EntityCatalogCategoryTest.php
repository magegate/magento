<?php

use \Magegate\Entity;
use \Magegate\EntityCatalogCategory;

class EntityCatalogCategoryTest extends EntityTest {

    public function testModel()
    {
        parent::testModel();
    }

    /**
     * @depends testModel
     */
    public function testConfig()
    {
        parent::testConfig();

        $host = $this->host;
        $this->assertNotEmpty($id=\Config::get($k="magento::config.entity.catalog/category.$host.id"),
            "Config $k not found");

        $this->assertNotEmpty($entity = Entity::helperLink2Mage($host,'catalog/category',0,$id),
            "Root Entity for $id not found");
    }

    /**
     * @depends testConfig
     */
    public function testPathHostIdList()
    {
        $host = $this->host;
        $list = EntityCatalogCategory::helperProvidePathHostIdList(
            $this->providerPathHostIdList());

        foreach($list as $i=>$data)
        {
            $list[$i] = $data = EntityCatalogCategory::helperHost2Mage($host,$data);

            $this->assertTrue(isset($data[$key='category_id']),"$key in item not found");
            $this->assertTrue(isset($data[$key='name']),"$key in item not found");
            $this->assertTrue(isset($data[$key='parent_id']),"$key in item not found");
            $this->assertTrue(isset($data[$key='default_sort_by']),"$key in item not found");
            $this->assertTrue(isset($data[$key='available_sort_by']),"$key in item not found");
            $this->assertTrue(isset($data[$key='is_active']),"$key in item not found");
            $this->assertTrue(isset($data[$key='include_in_menu']),"$key in item not found");

            $this->assertTrue(is_array($data[$key='parent_id']),"$key in item is not an array");
            $this->assertTrue(is_array($data[$key='available_sort_by']),"$key in item is not an array");
            $this->assertTrue(in_array($data[$key1='default_sort_by'],$data[$key2='available_sort_by']),"$key1 in item not found in $key2");
        }

       return $list;
    }

    /**
     * @depends testPathHostIdList
     */
    public function testSortListPathHostIdList()
    {
        $list = EntityCatalogCategory::helperSortList($this->testPathHostIdList());
        $done = array(0=>'Root Category');
        foreach($list as $i=>$data)
        {
            $category_id = $data['category_id'];
            foreach($data['parent_id'] as $parent_id)
            {
                $this->assertTrue(array_key_exists($parent_id,$done),
                    "$i: $parent_id not previously created before $category_id\n");
            }
            $done[$category_id] = $data['name'];
        }

        return $list;
    }

    public function providerCatalogCategoryList()
    {
        return EntityCatalogCategory::helperProvidePathHostIdList(
            $this->providerCatalogCategoryPathHostIdList());
    }

    public function providerHostIdNameParentsList()
    {
        return array(
            array('3311301','Backzubehör','3311261','3311201'),
            array('3094754031','Abkühlgitter','3311301'),
            array('1390632031','Ausstechformen','3311301'),
            array('3094756031','Backformen für Kinder','3311301'),
            array('3094757031','Backpapier','3311301'),
            array('257419011','Backunterlagen','3311301'),
            array('3094758031','Cupcake Förmchen','3311301'),

            array('257421011','Deko Utensilien','3311301'),
            array('257418011','Backpinsel','257421011'),
            array('3094761031','Kuchenaufsätze','257421011'),
            array('3094762031','Modellierwerkzeug','257421011'),
            array('3094763031','Schablonen','257421011'),
            array('3094764031','Spritzbeutel','257421011'),
            array('3094765031','Spritztüllen','257421011'),
            array('3311481','Wender','257421011'),

            array('3094766031','Gebäckpresse','3311301'),
            array('3094767031','Handschneebesen','3311301'),
            array('524467031','Kuchenbehälter','3311301'),
            array('3094770031','Kuchenringe & -rahmen','3311301'),
            array('3094772031','Mehlsiebe','3311301'),
            array('3094771031','Messbecher','3311301'),
            array('3094773031','Teigrad','3311301'),
            array('257420011','Teigroller','3311301'),
            array('257423011','Tortenbutler','3311301'),
        );
    }

    public function providerPathHostIdList()
    {
        return array(
            'Backen' => '3311261',
            'Backen/Backbleche & -gitter' => '3094738031',
            'Backen/Backbleche & -gitter/Bleche' => '3311281',
            'Backen/Backbleche & -gitter/Gitter' => '3094739031',
            'Backen/Backbleche & -gitter/Pizzableche' => '3094740031',
            'Backen/Backbleche & -gitter/Pizzasteine' => '3094741031',
/*
            'Backen/Backformen' => '3311291',
            'Backen/Backformen/Auflauf- & Souffléförmchen' => '3094742031',
            'Backen/Backformen/Back- & Tortenbodenformen' => '257414011',
            'Backen/Backformen/Back- & Tortenbodenformen/Besondere Backformen' => '3094743031',
            'Backen/Backformen/Back- & Tortenbodenformen/Guglhupf-Backformen' => '257413011',
            'Backen/Backformen/Back- & Tortenbodenformen/Kastenformen' => '3094744031',
            'Backen/Backformen/Back- & Tortenbodenformen/Kranz-Backformen' => '3094745031',
            'Backen/Backformen/Back- & Tortenbodenformen/Obstkuchen- & Tortenbodenformen' => '3094746031',
            'Backen/Backformen/Back- & Tortenbodenformen/Springformen' => '257411011',
            'Backen/Backformen/Backformsets' => '257416011',
            'Backen/Backformen/Brotbackformen' => '257412011',
            'Backen/Backformen/Cake Pop Formen' => '3094748031',
            'Backen/Backformen/Muffin-Backformen' => '257415011',
            'Backen/Backformen/Pralinenformen' => '3094751031',
            'Backen/Backformen/Tarte- & Quicheformen' => '3094752031',
            'Geschirr, Besteck & Gläser' => '3310821',
            'Geschirr, Besteck & Gläser/Besteck' => '3310831',
            'Geschirr, Besteck & Gläser/Besteck/Bestecksets' => '3098646031',
            'Geschirr, Besteck & Gläser/Besteck/Essstäbchen' => '3098645031',
            'Geschirr, Besteck & Gläser/Besteck/Essstäbchen-Ablagen' => '3098644031',
            'Geschirr, Besteck & Gläser/Besteck/Gabeln' => '3098649031',
            'Geschirr, Besteck & Gläser/Besteck/Gabeln/Cocktailgabeln' => '3098652031',
            'Geschirr, Besteck & Gläser/Besteck/Gabeln/Dessertgabeln' => '3098653031',
            'Geschirr, Besteck & Gläser/Besteck/Gabeln/Fischgabeln' => '3098655031',
            'Geschirr, Besteck & Gläser/Besteck/Gabeln/Fonduegabeln' => '3098656031',
            'Geschirr, Besteck & Gläser/Besteck/Gabeln/Kuchengabeln' => '3098650031',
            'Geschirr, Besteck & Gläser/Besteck/Gabeln/Obstgabeln' => '3098657031',
            'Geschirr, Besteck & Gläser/Besteck/Gabeln/Pastagabeln' => '3098658031',
            'Geschirr, Besteck & Gläser/Besteck/Gabeln/Tafelgabeln' => '3098654031',
            'Geschirr, Besteck & Gläser/Besteck/Kinderbesteck' => '3098643031',
            'Geschirr, Besteck & Gläser/Besteck/Löffel' => '3098700031',
            'Geschirr, Besteck & Gläser/Besteck/Meeresfrüchtebesteck' => '3098672031',
            'Geschirr, Besteck & Gläser/Besteck/Messer' => '3098659031',
            'Geschirr, Besteck & Gläser/Besteck/Messer/Buttermesser' => '3098660031',
            'Geschirr, Besteck & Gläser/Besteck/Messer/Dessertmesser' => '3098662031',
            'Geschirr, Besteck & Gläser/Besteck/Messer/Fischmesser' => '3098664031',
            'Geschirr, Besteck & Gläser/Besteck/Messer/Käsemesser' => '3098661031',
            'Geschirr, Besteck & Gläser/Besteck/Messer/Messerbänke' => '3098665031',
            'Geschirr, Besteck & Gläser/Besteck/Messer/Steakmesser' => '3098666031',
            'Geschirr, Besteck & Gläser/Besteck/Messer/Tafelmesser' => '3098663031',
            'Geschirr, Besteck & Gläser/Besteck/Messer/Unbestückte Messerblöcke' => '3122167031',
            'Geschirr, Besteck & Gläser/Besteck/Schöpflöffel & -kellen' => '3098667031',
            'Geschirr, Besteck & Gläser/Besteck/Servierbesteck' => '3098679031',
            'Geschirr, Besteck & Gläser/Besteck/Servierbesteck/Fischheber' => '3098683031',
            'Geschirr, Besteck & Gläser/Besteck/Servierbesteck/Kuchen- & Tortenheber' => '3098681031',
            'Geschirr, Besteck & Gläser/Besteck/Servierbesteck/Salatbesteck' => '3098684031',
            'Geschirr, Besteck & Gläser/Besteck/Servierbesteck/Spargelzangen & -heber' => '3098680031',
            'Geschirr, Besteck & Gläser/Besteck/Servierlöffel & -zangen' => '3098685031',
            'Geschirr, Besteck & Gläser/Besteck/Servierlöffel & -zangen/Salatzangen' => '3098688031',
            'Geschirr, Besteck & Gläser/Besteck/Servierlöffel & -zangen/Saucenlöffel' => '3098690031',
            'Geschirr, Besteck & Gläser/Besteck/Servierlöffel & -zangen/Servierzangen' => '3098686031',
            'Geschirr, Besteck & Gläser/Besteck/Servierlöffel & -zangen/Spaghettilöffel & Pastaheber' => '3098691031',
            'Geschirr, Besteck & Gläser/Besteck/Servierlöffel & -zangen/Zuckerlöffel' => '3098692031',
            'Geschirr, Besteck & Gläser/Besteck/Servierlöffel & -zangen/Zuckerzangen' => '3098693031',
            'Geschirr, Besteck & Gläser/Einweggeschirr' => '3098783031',
            'Geschirr, Besteck & Gläser/Einweggeschirr/Becher' => '3098786031',
            'Geschirr, Besteck & Gläser/Einweggeschirr/Gabeln' => '3098785031',
            'Geschirr, Besteck & Gläser/Einweggeschirr/Löffel' => '3098788031',
            'Geschirr, Besteck & Gläser/Einweggeschirr/Messer' => '3098787031',
            'Geschirr, Besteck & Gläser/Einweggeschirr/Teller' => '3098784031',
            'Geschirr, Besteck & Gläser/Geschirr' => '3310911',
            'Geschirr, Besteck & Gläser/Geschirr/Eierbecher' => '3098744031',
            'Geschirr, Besteck & Gläser/Geschirr/Essig & Öl' => '3098745031',
            'Geschirr, Besteck & Gläser/Geschirr/Milch- & Zuckerbehälter' => '3098729031',
            'Geschirr, Besteck & Gläser/Geschirr/Schalen & Schüsseln' => '3098719031',
            'Geschirr, Besteck & Gläser/Geschirr/Serviergeschirr' => '3098747031',
            'Geschirr, Besteck & Gläser/Geschirr/Tassen & Untertassen' => '3098733031',
            'Geschirr, Besteck & Gläser/Geschirr/Tee- & Kaffeekannen' => '3098777031',
            'Geschirr, Besteck & Gläser/Geschirr/Tischuntersetzer' => '3098776031',
            'Geschirr, Besteck & Gläser/Gläser' => '3311101',
            'Geschirr, Besteck & Gläser/Service & Geschirrsets' => '3098713031',
            'Geschirr, Besteck & Gläser/Service & Geschirrsets/Kaffeeservice' => '3098715031',
            'Geschirr, Besteck & Gläser/Service & Geschirrsets/Kindergeschirr' => '3098714031',
            'Geschirr, Besteck & Gläser/Service & Geschirrsets/Kombiservice' => '3098716031',
            'Geschirr, Besteck & Gläser/Service & Geschirrsets/Tafelservice' => '3098717031',
            'Geschirr, Besteck & Gläser/Service & Geschirrsets/Teeservice' => '3098718031',
            'Geschirr, Besteck & Gläser/Serviettenringe' => '3098811031',
            'Kochen' => '3094896031',
            'Kochen/Deckel' => '339747011',
            'Kochen/Feuerzangenbowle' => '3094958031',
            'Kochen/Fondues' => '3429941',
            'Kochen/Fondues/Fondue-Fritteusen' => '342069011',
            'Kochen/Fondues/Fondue-Sets' => '342070011',
            'Kochen/Fondues/Fondue-Zubehör' => '342071011',
            'Kochen/Fondues/Käse-Fondues' => '342073011',
            'Kochen/Fondues/Schoko-Fondues' => '342072011',
            'Kochen/Kochzubehör' => '3311841',
            'Kochen/Kochzubehör/Dampf- & Dünsteinsätze' => '341828011',
            'Kochen/Kochzubehör/Henkel & Griffe' => '3095006031',
            'Kochen/Kochzubehör/Schnellkochtopf Zubehör' => '3095008031',
            'Kochen/Kochzubehör/Spritzschutz' => '339748011',
            'Kochen/Kochzubehör/Untersetzer' => '3312011',
            'Kochen/Ofenformen' => '3094898031',
            'Kochen/Ofenformen/Auflaufformen' => '3311221',
            'Kochen/Ofenformen/Brotbackformen' => '257412011',
            'Kochen/Ofenformen/Quicheformen' => '3094752031',
            'Kochen/Ofenformen/Souffléförmchen' => '3094742031',
            'Kochen/Stövchen & Speisewärmer' => '3312211',
            'Kochen/Tajines' => '3094956031',
            'Kochen/Terrinen' => '3094957031',
            'Kochen/Töpfe & Pfannen' => '3311941',
*/
            'Küchenhelfer & Kochzubehör' => '3311201',
            'Küchenhelfer & Kochzubehör/Abdeck- & Fliegenhauben' => '3177925031',
            'Küchenhelfer & Kochzubehör/Arbeits- & Kochplattenabdeckung' => '3177984031',
            'Küchenhelfer & Kochzubehör/Bar-Accessoires' => '3312051',
            'Küchenhelfer & Kochzubehör/Bratenspritzen' => '3177918031',
            'Küchenhelfer & Kochzubehör/Brotbretter' => '3177919031',
            'Küchenhelfer & Kochzubehör/Dosen- & Deckelöffner' => '3311541',
            'Küchenhelfer & Kochzubehör/Dressing-Shaker' => '3469881',
            'Küchenhelfer & Kochzubehör/Eierschneider' => '3177924031',
            'Küchenhelfer & Kochzubehör/Eisformen' => '3177933031',
            'Küchenhelfer & Kochzubehör/Eisportionierer' => '3177934031',
            'Küchenhelfer & Kochzubehör/Flaschenausgießer' => '3177970031',
            'Küchenhelfer & Kochzubehör/Fleisch- & Grillgabeln' => '3311411',
            'Küchenhelfer & Kochzubehör/Fleischhämmer & -klopfer' => '3469891',
/*
            'Küchenhelfer & Kochzubehör/Geflügelscheren' => '3311551',
            'Küchenhelfer & Kochzubehör/Gemüsehobel' => '3177966031',
            'Küchenhelfer & Kochzubehör/Gewürzspender' => '3311331',
            'Küchenhelfer & Kochzubehör/Grill- & Schaschlikspieße' => '3177980031',
            'Küchenhelfer & Kochzubehör/Hackbretter' => '3177921031',
            'Küchenhelfer & Kochzubehör/Haken, Leisten & Regale' => '257425011',
            'Küchenhelfer & Kochzubehör/Hobel & Reiben' => '3311571',
            'Küchenhelfer & Kochzubehör/Kartoffel- & Spätzlepressen' => '3177977031',
            'Küchenhelfer & Kochzubehör/Kartoffelschneider' => '3177976031',
            'Küchenhelfer & Kochzubehör/Knoblauchpressen' => '3177931031',
            'Küchenhelfer & Kochzubehör/Küchenbrenner' => '3177935031',
            'Küchenhelfer & Kochzubehör/Küchenmesser' => '3311721',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Asiatische Messer' => '3177937031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Ausbeinmesser' => '3177939031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Brotmesser' => '3177940031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Filetiermesser' => '3177947031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Hackmesser' => '3177944031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Kochmesser' => '3177943031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Kuchen- & Tortenmesser' => '3177941031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Messerblocksets' => '3177938031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Obst- & Gemüsemesser' => '3177948031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Schinkenmesser' => '3177949031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Schälmesser' => '3177953031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Sets' => '3177950031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Spickmesser' => '3177954031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Tranchiermesser' => '3177942031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Unbestückte Messerblöcke' => '3122167031',
            'Küchenhelfer & Kochzubehör/Küchenmesser/Wiegemesser' => '3177951031',
            'Küchenhelfer & Kochzubehör/Küchenscheren' => '10825371',
            'Küchenhelfer & Kochzubehör/Küchenthermometer' => '10704461',
            'Küchenhelfer & Kochzubehör/Küchenthermometer/Bratenthermometer' => '3177960031',
            'Küchenhelfer & Kochzubehör/Küchenthermometer/Einkochthermometer' => '3177963031',
            'Küchenhelfer & Kochzubehör/Küchenthermometer/Ofenthermometer' => '3177962031',
            'Küchenhelfer & Kochzubehör/Küchenuhren & Timer' => '10704441',
            'Küchenhelfer & Kochzubehör/Küchenwaagen' => '10704431',
            'Küchenhelfer & Kochzubehör/Lebensmittelaufbewahrung' => '3311661',
            'Küchenhelfer & Kochzubehör/Löffel, Spachtel & Wender' => '3311401',
            'Küchenhelfer & Kochzubehör/Löffel, Spachtel & Wender/Kochlöffel' => '3311421',
            'Küchenhelfer & Kochzubehör/Löffel, Spachtel & Wender/Schöpflöffel & -kellen' => '3098667031',
            'Küchenhelfer & Kochzubehör/Löffel, Spachtel & Wender/Spachtel' => '3177982031',
            'Küchenhelfer & Kochzubehör/Löffel, Spachtel & Wender/Spaghettilöffel & Pastaheber' => '3098691031',
            'Küchenhelfer & Kochzubehör/Löffel, Spachtel & Wender/Wender' => '3311481',
            'Küchenhelfer & Kochzubehör/Löffel, Spachtel & Wender/Zangen' => '3311491',
            'Küchenhelfer & Kochzubehör/Meeresfrüchte-Werkzeuge' => '3177978031',
            'Küchenhelfer & Kochzubehör/Messbecher & Maße' => '10704451',
            'Küchenhelfer & Kochzubehör/Messerschärfer & -pflege' => '3311771',
            'Küchenhelfer & Kochzubehör/Messerschärfer & -pflege/Manuelle Messerschärfer' => '3177964031',
            'Küchenhelfer & Kochzubehör/Messerschärfer & -pflege/Wetzstähle' => '3177965031',
            'Küchenhelfer & Kochzubehör/Messlöffel-Sets' => '3177968031',
            'Küchenhelfer & Kochzubehör/Mörser & Stößel Sets' => '3177969031',
            'Küchenhelfer & Kochzubehör/Mühlen & Mörser' => '3311581',
            'Küchenhelfer & Kochzubehör/Nudelwerkzeuge' => '3177971031',
            'Küchenhelfer & Kochzubehör/Nudelwerkzeuge/Nudelmaschinen' => '1390650031',
            'Küchenhelfer & Kochzubehör/Nudelwerkzeuge/Nudeltrockner' => '3177972031',
            'Küchenhelfer & Kochzubehör/Nussknacker' => '3469911',
            'Küchenhelfer & Kochzubehör/Obstschäler & Schneider' => '3177926031',
            'Küchenhelfer & Kochzubehör/Obstschäler & Schneider/Apfelschäler' => '3177927031',
            'Küchenhelfer & Kochzubehör/Obstschäler & Schneider/Entkerner' => '3177928031',
            'Küchenhelfer & Kochzubehör/Obstschäler & Schneider/Obstschneider' => '3177929031',
            'Küchenhelfer & Kochzubehör/Pizzaschneider' => '3177975031',
            'Küchenhelfer & Kochzubehör/Reinigungsprodukte' => '3463231',
            'Küchenhelfer & Kochzubehör/Rollenhalter' => '257424011',
            'Küchenhelfer & Kochzubehör/Saft- & Zitruspressen' => '3311591',
            'Küchenhelfer & Kochzubehör/Sahnespender' => '3094832031',
            'Küchenhelfer & Kochzubehör/Salatschleudern' => '3311871',
            'Küchenhelfer & Kochzubehör/Schaum- & Abseihlöffel' => '3311441',
            'Küchenhelfer & Kochzubehör/Schinkenhalter' => '3177932031',
            'Küchenhelfer & Kochzubehör/Schneebesen' => '3311431',
            'Küchenhelfer & Kochzubehör/Schneidbretter' => '3177923031',
            'Küchenhelfer & Kochzubehör/Schäler' => '3177974031',
            'Küchenhelfer & Kochzubehör/Seiher' => '3177922031',
            'Küchenhelfer & Kochzubehör/Siebe & Spitzsiebe' => '3177979031',
            'Küchenhelfer & Kochzubehör/Spritzschutz' => '339748011',
            'Küchenhelfer & Kochzubehör/Stampfer' => '3177967031',
            'Küchenhelfer & Kochzubehör/Tee- & Wasserkessel' => '3311391',
            'Küchenhelfer & Kochzubehör/Trichter' => '3311621',
            'Küchenhelfer & Kochzubehör/Untersetzer' => '3312011',
*/
        );
    }

}