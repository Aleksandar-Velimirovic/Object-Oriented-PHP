<?php

class Product {

    private $id;
    private $name;
    private $price;
    private $ownerId;

    public function __construct(int $id, string $name, int $price, int $ownerId){
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->ownerId = $ownerId;
    }

    public function getName(){
        return $this->name;
    }

    public function getId(){
        return $this->id;
    }

    public function getOwnerId(){
        return $this->ownerId;
    }

    public function setOwnerId($id){
        return $this->ownerId = $id;
    }
}

class User {
    private $id;
    private $name;

    public function __construct(int $id, string $name){
        $this->id = $id;
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }

    public function getId(){
        return $this->id;
    }
}

class RelationUserProduct {
    public $productId;
    public $customerId;

    public function __construct(int $productId, int $customerId){
        $this->productId = $productId;
        $this->customerId = $customerId;
    }
}

class Bid extends RelationUserProduct {

    public $moneyAmount;

    public function __construct(int $productId, int $customerId, int $moneyAmount){
        parent::__construct($productId, $customerId);
        $this->moneyAmount = $moneyAmount;
    }
}

class Wish extends RelationUserProduct {

}

class AuctionMarketPlace {
    private static $instance;
    private $products;
    private $users;
    private $wishlist;
    private $bids;

    public static function getInstance(){
        if(self::$instance == null){
            return self::$instance = new AuctionMarketPlace();
        }

        return self::$instance;
    }

    public function addProduct(Product $product){
        $this->products[$product->getId()] = $product;
    }

    public function addUser(User $user){
        $this->users[$user->getId()] = $user;
    }

    public function addProductToWishlist(int $productId, int $customerId){
        
        $this->wishlist[] = new Wish($productId, $customerId);

        echo '<span style="color:blue"> User ' . $this->users[$customerId]->getName() . ' added ' . $this->products[$productId]->getName() . ' to wishlist. </span>';

        return;
    }

    public function removeProductFromWishlist(int $productId, int $customerId){

        foreach($this->wishlist as $index => $wish){
            if($wish->productId == $productId && $wish->customerId == $customerId){
                unset($this->wishlist[$index]);
            }
        }

        return;
    }

    public function productBid(int $productId, int $customerId, int $amount){

        $this->addProductToWishlist($productId, $customerId);

        $this->bids[] = new Bid($productId, $customerId, $amount);

        echo '<span style="color:blue"> User: ' . $this->users[$customerId]->getName() . ' offers ' . $amount . ' for product: ' . $this->products[$productId]->getName() . '</span>';

        return;
    }

    public function withdrawProductBid(int $productId, int $customerId){

        foreach($this->bids as $index => $bid){
            if($bid->productId == $productId && $bid->customerId == $customerId){
                unset($this->bids[$index]);
            }
        }

        echo '<span style="color:blue"> User: ' . $this->users[$customerId]->getName() . ' withdraws bid for product: ' . $this->products[$productId]->getName() . '</span>';

        return;
    }

    public function sellProduct(int $productId, int $customerId){
        
        foreach ($this->bids as $index => $bid) {
            if ($bid->productId === $productId && $bid->customerId === $customerId) {
    
                unset($this->bids[$index]);

                $previousOwnerId = $this->products[$productId]->getOwnerId();

                $this->products[$productId]->setOwnerId($customerId);

                $this->removeProductFromWishlist($productId, $customerId);

                $product = $this->products[$productId];

                echo $this->users[$previousOwnerId]->getName() . ' sold ' . $product->getName() . ' to ' .  $this->users[$product->getOwnerId()]->getName();

                return;
            }
        }
    }
}

$user1 = new User(1, 'Marko Markovic');
$user2 = new User(2, 'Nikola Nikolic');
$user3 = new User(3, 'Ivan Ivanovic');
$user4 = new User(4, 'Jovan Jovanovic');

$product1 = new Product(1, 'laptop', 1000, 4);

AuctionMarketplace::getInstance()->addUser($user1);
AuctionMarketplace::getInstance()->addUser($user2);
AuctionMarketplace::getInstance()->addUser($user3);
AuctionMarketplace::getInstance()->addUser($user4);

AuctionMarketplace::getInstance()->addProduct($product1);

AuctionMarketplace::getInstance()->addProductToWishlist($product1->getId(), $user1->getId());

AuctionMarketplace::getInstance()->productBid($product1->getId(), $user2->getId(), 1200);

AuctionMarketplace::getInstance()->productBid($product1->getId(), $user3->getId(), 1400);

AuctionMarketplace::getInstance()->sellProduct($product1->getId(), $user3->getId());

// echo '<pre>';
// var_dump(AuctionMarketplace::getInstance());