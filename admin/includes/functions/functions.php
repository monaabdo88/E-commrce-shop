<?php

/*
** Get All Function v2.0
** Function To Get All Records From Any Database Table
*/
function getAllFrom($field,$tbl,$where = NULL,$and=NULL,$orderField,$order = 'DESC'){
    global $con;
    $stmt = $con->prepare("SELECT $field FROM $tbl $where $and ORDER BY $orderField $order");
    $stmt->execute();
    $getAll = $stmt->fetchAll();
    return $getAll;
}
/*
	** Title Function v1.0
	** Title Function That Echo The Page Title In Case The Page
	** Has The Variable $pageTitle And Echo Defult Title For Other Pages
*/
function getTitle(){
    global $pageTitle;
    if(isset($pageTitle)) echo $pageTitle; else echo 'Default';
}
/*
	** Home Redirect Function v2.0
	** This Function Accept Parameters
	** $theMsg = Echo The Message [ Error | Success | Warning ]
	** $url = The Link You Want To Redirect To
	** $seconds = Seconds Before Redirecting
*/
function redirectHome($msg,$url=null,$seconds = 3){
    if($url == null){
        $url = 'index.php';
        $link = "HomePage";
    }else{
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){
            $url = $_SERVER['HTTP_REFERER'];
            $link = "Previous Page";
        }else{
            $url = 'index.php';
            $link = "HomePage";
        }
    }
    echo $msg;
    echo "<div class='alert alert-info'>You Will Be Redirected to $link After $seconds Seconds.</div>";

    header("refresh:$seconds;url=$url");

    exit();
}
/*
	** Check Items Function v1.0
	** Function to Check Item In Database [ Function Accept Parameters ]
	** $select = The Item To Select [ Example: user, item, category ]
	** $from = The Table To Select From [ Example: users, items, categories ]
	** $value = The Value Of Select [ Example: Osama, Box, Electronics ]
*/
function checkItem($select,$from,$value){
    global $con;

    $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");

    $statement->execute(array($value));

    $count = $statement->rowCount();

    return $count;
}
/*
	** Count Number Of Items Function v1.0
	** Function To Count Number Of Items Rows
	** $item = The Item To Count
	** $table = The Table To Choose From
*/
function countItems($item,$tbl){
    global $con;
    $stmt = $con->prepare("SELECT COUNT($item) FROM $tbl");
    $stmt->execute();
    return $stmt->fetchColumn();
}
/*
	** Get Latest Records Function v1.0
	** Function To Get Latest Items From Database [ Users, Items, Comments ]
	** $select = Field To Select
	** $table = The Table To Choose From
	** $order = The Desc Ordering
	** $limit = Number Of Records To Get
*/
function getLatest($select,$tbl,$order,$limit = 5){
    global $con;
    $stmt = $con->prepare("SELECT $select FROM $tbl ORDER BY $order LIMIT $limit");
    $stmt->execute();
    $row = $stmt->fetchAll();
    return $row;
}