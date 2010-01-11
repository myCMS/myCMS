<?php
/**
 * Module Catalogue.
 *
 * @see EngineCore.php
 * @author AlexK
 * @version 1.0
 */
class ModuleCatalogueExt extends ModuleCatalogue {

    /**
     * Constructor of class ContentExt
     */
    public function  __construct() {
        ;
    }

    /**
     * Return list of Catalogue list
     *
     * @param   nothing
     * @throws  taken from ModuleCatalogue
     */
    /*public function getSingle(){

        if (!$this->isSingleDisplay()){
            return "";
        }
            //return parent::getList();
		$result = array();
        $table  = '';
        $catId  = 0;
        $catId = $this->catId;
        $table = 'cat_products';

        $this->MySQL->query("SELECT
                                cat_products.id AS product_id,
                                cat_products.name AS product_name,
                                cat_types.name AS type_name,
                                cat_products.description AS product_description,
                                cat_products.article AS product_article,
                                cat_products.exist AS product_exist,
                                cat_brend.name AS brend_name,
                                cat_brend.description AS brend_description,
                                cat_price.product,
                                cat_price.price AS product_price,
                                cat_price.`type` AS price_type,
                                cat_types.description AS type_description,
                                cat_types.parent_id
                            FROM
                                ". $table ."
                                Left Join cat_price ON cat_products.id = cat_price.product
                                Left Join cat_brend ON cat_products.brend = cat_brend.id ,
                                cat_types
                                Inner Join cat_link_products_types ON cat_products.id = cat_link_products_types.product_id AND cat_link_products_types.types_id = cat_types.id
                            WHERE
                                cat_products.active = 1 AND cat_products.id = " .$catId ."");

        while ($row = $this->MySQL->fetchArray()){

            $dir = SMARTY_MODULE_CATALOGUE_IMG_FOLDER . $row['product_id'];
            if (@is_dir($dir)){
                if ($dh = @opendir($dir)){
                    while (($file = readdir($dh)) !== false){
                        if ($file != '.' AND $file != '..'){
                            $File_Size = @getimagesize($file);
                            $img[] = array(
                                            'src' => $dir .'/'. $file,
                                            'size' => $File_Size[3]
                            );
                        }
                    }
                    @closedir($dh);
                }
            }
            else{
                $file = SMARTY_MODULE_CATALOGUE_IMG_FOLDER . '00.gif';
                $File_Size = @getimagesize($file);
                $img[] = array(
                                'src' => $file,
                                'size' => $File_Size[3]
                );
            }

            $result = array(
                            'img'				=>	$img,
                            'url'				=>	"catId=". $row['product_id'],
                            'name'				=>	$row['product_name'],
                            'description'		=>	$row['product_description'],
                            'article'			=>	$row['product_article'],
                            'exist'				=>	$row['product_exist'],
                            'brend_name'		=>	$row['brend_name'],
                            'brend_description'	=>	$row['brend_description'],
                            'price'				=>	$row['product_price'],
                            'price_type'		=>	$row['price_type'],
                            'type'				=>	$row['type_name'],
                            'types_description'	=>	$row['type_description'],
							'buy_button'		=>	'<a href=""><img src="img/y_bl_clock.gif"><br>buy</>',
            );
        }

        $this->MySQL->freeResult();

        return $result;

    }*/
}
?>
