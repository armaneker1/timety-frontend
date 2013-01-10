<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;


class Neo4jUserSettingsUtil{
    
    public static function  getUserSubscribeCategories($userId)
    {
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                ".out('" . REL_SUBSCRIBES . "').dedup";
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $cat = new CateforyRef();
            $cat->createNeo4j($row[0]);
            array_push($array, $cat);
        }
        return $array;
    }
    
    
    public static function  getUserSubscribeCategory($userId,$categoryId)
    {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                 ".out('" . REL_SUBSCRIBES . "').dedup.has['".PROP_CATEGORY_ID."',".$categoryId."]";
         var_dump($query);
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $cat = new CateforyRef();
            $cat->createNeo4j($row[0]);
            return $cat;
        }
        return null;
    }
    
    public static function addUserSubscribeCategory($userId,$categoryId)
    {
         $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
         $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
         $categoryIndex = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL2);
         $user = $userIndex->findOne(PROP_USER_ID, $userId);
         $cat=$categoryIndex->findOne(PROP_CATEGORY_ID, $categoryId);
         
         if(!empty($user) && !empty($cat))
         {
             $cat_tmp= Neo4jUserSettingsUtil::getUserSubscribeCategory($userId, $categoryId);
             if(empty($cat_tmp) || empty($cat_tmp->id))
             {
                 $user->relateTo($cat,REL_SUBSCRIBES)->save();
             }
             return true;
         }
         
         return false;
    }
    
    public static function  test($userId)
    {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.v(" . $userId . ")";
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            return $row[0];
        }
        return null;
    }
}
?>
