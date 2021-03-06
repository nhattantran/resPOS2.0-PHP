<div class="container mt-5 p-3 rounded cart">
    <div class="row no-gutters">
        <div class="col-md-8">
            <div class="product-details mr-2">
                <div class="d-flex flex-row align-items-center">
                    <a href="?action=order">
                        <i class="fa fa-long-arrow-left"></i><span class="ml-2">Continue Shopping</span>
                    </a>
                </div>
                <hr>
                <h6 class="mb-0">Shopping cart</h6>

                <!-- First item -->
                <?php
                $totalPrice = 0;
                if (isset($_SESSION['cart'])) {
                    if (isset($_GET['remove'])){
                        $removeID = $_GET['remove'];
                        foreach ($_SESSION['cart'] as $k => $v){
                            if ($k == $removeID){
                                unset($_SESSION['cart'][$k]);
                            }
                        }
                    }
                    foreach ($_SESSION['cart'] as $key => $value) {
                        $foodID = $key;
                        $sql = "SELECT foodID, name, price, description, image FROM food WHERE foodID = '$foodID'";
                        $res = $connect->query($sql);
                        if (empty($res) or $res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) {
                                $finalPrice = $row['price'] * $_SESSION['cart'][$key]['qty'];
                                $totalPrice += $finalPrice;
                                echo '<div class="d-flex justify-content-between align-items-center mt-3 p-2 items rounded">
                                <div class="col-md-7">
                                    <div class="d-flex flex-row">
                                        <img class="rounded" src="'.$row['image'].'" width="40">
                                        <div class="ml-2">
                                            <span class="font-weight-bold d-block">'.$row['name'].'</span>
                                            <span class="spec text-wrapped">'.$row['description'].'</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="col-md-4">
                                            <span class="d-block">'.$_SESSION['cart'][$key]['qty'].'</span>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="d-block ml-5 font-weight-bold">$'.$finalPrice.'</span>
                                        </div>
                                        <div class="col-md-2">
                                        <a href="?remove='.$foodID.'">
                                            <i class="fa fa-trash-o ml-3 text-black-50"></i>
                                        </a>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            }
                        }
                    }
                    
                } else {
                    echo 'No product yet';
                }
                
                ?>
                
            </div>
        </div>
        <div class="col-md-4">
            <div class="payment-info">
                <div class="d-flex justify-content-between align-items-center"><span>Card details</span></div><span class="type d-block mt-3 mb-1">Card type</span><label class="radio"> <input type="radio" name="card" value="payment" checked> <span><img width="30" src="https://img.icons8.com/color/48/000000/mastercard.png" /></span> </label>
                <label class="radio"> <input type="radio" name="card" value="payment"> <span><img width="30" src="https://img.icons8.com/officel/48/000000/visa.png" /></span> </label>
                <label class="radio"> <input type="radio" name="card" value="payment"> <span><img width="30" src="https://img.icons8.com/ultraviolet/48/000000/amex.png" /></span> </label>
                <label class="radio"> <input type="radio" name="card" value="payment"> <span><img width="30" src="https://img.icons8.com/officel/48/000000/paypal.png" /></span> </label>
                <div><label class="credit-card-label">Name on card</label><input type="text" class="form-control credit-inputs" placeholder="Name"></div>
                <div><label class="credit-card-label">Card number</label><input type="text" class="form-control credit-inputs" placeholder="0000 0000 0000 0000"></div>
                <div class="row">
                    <div class="col-md-6"><label class="credit-card-label">Date</label><input type="text" class="form-control credit-inputs" placeholder="12/24"></div>
                    <div class="col-md-6"><label class="credit-card-label">CVV</label><input type="text" class="form-control credit-inputs" placeholder="342"></div>
                </div>
                <hr class="line">
                <form method="post" action="mycart.php">
                    <div>
                        <label class="credit-card-label">Full name</label>
                        <input type="text" class="form-control credit-inputs" name="customerName" placeholder="Full name" required>
                    </div>
                    <div>
                        <label class="credit-card-label">Address</label>
                        <input type="text" class="form-control credit-inputs" name="customerAddr" placeholder="Address" required>
                    </div>
                    <div>
                        <label class="credit-card-label">Phone number</label>
                        <input type="text" class="form-control credit-inputs" name="customerPhoneNumber" placeholder="Phone number" required>
                    </div>
                    <hr class="line">
                    <div class="d-flex justify-content-between information"><span>Subtotal</span><span>$<?php echo $totalPrice; ?></span></div>
                    <div class="d-flex justify-content-between information"><span>Shipping</span><span>$0.00</span></div>
                    <div class="d-flex justify-content-between information"><span>Total(Incl. taxes)</span><span>$<?php echo $totalPrice; ?></span></div>
                    <button class="btn btn-primary btn-block d-flex justify-content-between mt-3" type="submit" name="placeOrder">
                        <span>$<?php echo $totalPrice; ?></span>
                        <span>Checkout<i class="fa fa-long-arrow-right ml-1"></i></span>
                    </button>
                </form>
                <?php
                    if (isset($_POST['placeOrder'])){
                        if (!empty($_SESSION['cart'])){

                            include './admin/connect.php';

                            $cartID = rand(1, 1000000);
                            $totalCost = $totalPrice;
                            $fullName = $_POST['customerName'];
                            $addr = $_POST['customerAddr'];
                            $phone = $_POST['customerPhoneNumber'];
                            
                            $sql = "INSERT INTO cart (cartID, totalCost, customerName, address, phoneNumber, email) VALUE ('$cartID', '$totalCost', '$fullName', ' $addr', '$phone', 'Guess')";

                            $res = $connect->query($sql);

                            if ($res){
                                echo 'Order placed successfully';
                            } else {
                                echo 'Order not placed successfully'.$connect->error;
                            }
                            
                            foreach ($_SESSION['cart'] as $k => $v){
                                $foodID = $k;
                                $quantity = $_SESSION['cart'][$k]['qty'];
                                $sql = "INSERT INTO ordering (cartID, foodID, quantity) VALUE ('$cartID', '$foodID', '$quantity')";

                                $connect->query($sql);
                            }
                            unset($_SESSION['cart']);
                        } else {
                            echo 'No product yet';
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
include 'action.php';
?>
<style>
    .payment-info {
        background: blue;
        padding: 10px;
        border-radius: 6px;
        color: #fff;
        font-weight: bold
    }

    .product-details {
        padding: 10px
    }

    body {
        background: #eee
    }

    .cart {
        background: #fff
    }

    .p-about {
        font-size: 12px
    }

    .table-shadow {
        -webkit-box-shadow: 5px 5px 15px -2px rgba(0, 0, 0, 0.42);
        box-shadow: 5px 5px 15px -2px rgba(0, 0, 0, 0.42)
    }

    .type {
        font-weight: 400;
        font-size: 10px
    }

    label.radio {
        cursor: pointer
    }

    label.radio input {
        position: absolute;
        top: 0;
        left: 0;
        visibility: hidden;
        pointer-events: none
    }

    label.radio span {
        padding: 1px 12px;
        border: 2px solid #ada9a9;
        display: inline-block;
        color: #8f37aa;
        border-radius: 3px;
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 300
    }

    label.radio input:checked+span {
        border-color: #fff;
        background-color: blue;
        color: #fff
    }

    .credit-inputs {
        background: rgb(102, 102, 221);
        color: #fff !important;
        border-color: rgb(102, 102, 221)
    }

    .credit-inputs::placeholder {
        color: #fff;
        font-size: 13px
    }

    .credit-card-label {
        font-size: 9px;
        font-weight: 300
    }

    .form-control.credit-inputs:focus {
        background: rgb(102, 102, 221);
        border: rgb(102, 102, 221)
    }

    .line {
        border-bottom: 1px solid rgb(102, 102, 221)
    }

    .information span {
        font-size: 12px;
        font-weight: 500
    }

    .information {
        margin-bottom: 5px
    }

    .items {
        -webkit-box-shadow: 5px 5px 4px -1px rgba(0, 0, 0, 0.25);
        box-shadow: 5px 5px 4px -1px rgba(0, 0, 0, 0.08)
    }

    .spec {
        font-size: 11px
    }
</style>