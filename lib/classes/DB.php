<?php
    
    class DB {
        private $dbName;
        private $dbUser;
        private $dbPass;

        public function __construct($dbName, $dbUser, $dbPass) {
            $this->dbName = $dbName;
            $this->dbUser = $dbUser;
            $this->dbPass = $dbPass;
        }

        // метод добавляет пункт меню в БД
        public function addMenuItem($categoryName='', $parentName='главный') {
            try {
                //Входные данные приводим в единый формат(убираем пробелы и приводим строку к lower)
                $categoryName = trim(mb_strtolower($categoryName, 'UTF-8'));
                $parentName = trim(mb_strtolower($parentName, 'UTF-8'));
                
                if(is_string($categoryName) && !empty($categoryName)) {
                
                    if(!empty($categoryName)) {
                        //устанавливаем подключение
                        $conn = new PDO('mysql:host=localhost;dbname='.$this->dbName, $this->dbUser, $this->dbPass);

                        //создаем таблицу с категориями, если ее нет
                        $sqlCategoryTable = "CREATE TABLE IF NOT EXISTS Categories(
                        id SERIAL primary key, category_name varchar(100) NOT NULL, parent_name varchar(100) NOT NULL, parent_id INT DEFAULT NULL) 
                        CHARACTER SET utf8 COLLATE utf8_general_ci";

                        $conn->exec($sqlCategoryTable);

                        //проверяем нет ли такого значения в БД
                        $serchSqlElemUnic = $conn->prepare("SELECT category_name, parent_name FROM categories WHERE `category_name` = :category_name AND `parent_name` = :parent_name");
                        $serchSqlElemUnic->execute(array('category_name' => $categoryName, 'parent_name' =>  $parentName));
                        $unicResult = !empty($serchSqlElemUnic->fetch(PDO::FETCH_ASSOC)) ? false : true;

                        if($unicResult) {
                            // генерим id родителя и добавляем категорию в БД
                            $parentID = $conn->prepare("SELECT id FROM categories WHERE `category_name` = :parent_name");
                            $parentID->execute(array('parent_name' =>  $parentName));
                            $parentID = $parentID->fetch(PDO::FETCH_ASSOC);
                            $parentID['id'] = !empty($parentID['id']) ? $parentID['id'] : null;

                            $addElem = $conn->prepare("INSERT INTO `categories` SET `category_name` = :category_name, `parent_name` = :parent_name, `parent_id` = :parent_id");
                            $addElem->execute(array('category_name' => $categoryName, 'parent_name' => $parentName, 'parent_id' => $parentID['id']));
                            
                            print $categoryName . ' - Успешно добавлена!';

                            // разрываем подключение с бд
                            $conn = null;

                        } else {
                            // print 'Извините, ' . $categoryName . ' - уже существует';
                            //разрываем подключение с бд
                            $conn = null;
                        }
                    }

                } else {
                    if(is_string($categoryName) == false) print("{$categoryName} - некорректное имя категории");
                    if(empty($categoryName)) print("Введите имя категории");
                }


            } catch(PDOException $pdoError) {
                // вызывем ошибку в случае сбоя подключения
                die($pdoError->getMessage());
            }
        }

        // метод создает список для меню
        public function createMenu() {
            try {
                //устанавливаем подключение
                $conn = new PDO('mysql:host=localhost;dbname='.$this->dbName, $this->dbUser, $this->dbPass);
                $menuListSql = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
                

                $menuList = [];

                foreach ($menuListSql as $index => &$elem) {    
                    //Если нет вложений
                    if (empty($elem['parent_id'])){
                        $menuList[$index] = &$elem;
                    }else{ 
                        //Если есть потомки то перебераем массив("-1" потому что в БД пишется индекс с 1)
                        $menuListSql[$elem['parent_id'] - 1]['childs'][$index] = &$elem;
                    }
                }

                $conn = null;
                return $this->createMenuView($menuList);

            } catch(PDOException $pdoError) {
                // вызывем ошибку в случае сбоя подключения
                die($pdoError->getMessage());
            }
            
        }

        // метод-шаблонизатор меню
        private function createTplMenu($item) {
            $menu = '<li><span class="item' . (empty($item['parent_id']) ? ' main-item' : '') . (isset($item['childs']) ? ' check-menu-wrapp' : '') . '"><span>'. $item['category_name'] . '</span>';
                
                if(isset($item['childs'])){
                    $menu .= '<i class="child-checker"></i>';
                    $menu .= '</span>';
                    $menu .= '<ul class="child-wrapp">'. $this->createMenuView($item['childs']) .'</ul>';
                } else {
                    $menu .= '</span>';
                }

            $menu .= '</li>';
            
            return $menu;
        }

        //рекурсивный перебор для генерации меню
        private function createMenuView($data) {
            $string = '';
            foreach($data as $item){
                $string .= $this->createTplMenu($item);
            }
            return $string;
        }
    }