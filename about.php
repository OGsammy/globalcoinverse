<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="CSS/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/aos.css">
    <link rel="stylesheet" href="CSS/toastr.css">
    <link rel="stylesheet" href="CSS/all.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/swiper-bundle.min.css">
    <!-- main css for template -->
    <link rel="stylesheet" href="CSS/style.css">
</head>
<style>
    .pos{
     z-index: 1;
     position: fixed !important;
     top: 82px;
 }

 .section {
  position: relative;
  overflow: hidden;
  z-index: 1;
}

.section::before {
  content: "";
  background-image: url('Images2/asa-e-k-13EsoonqDkY-unsplash.jpg'); /* Replace with your image */
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  opacity: 0.2; /* ðŸ‘ˆ More visible */
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}

* 🌍 Base page colors */
* 🌍 Base page colors */





header.header-section a:hover {
  color: #ffd700; /* Optional hover color */
}


/* 🧭 Header Section Styling */
header.header-section {
  background-color: var(--header-bg-color);
  color: var(--header-text-color);
  transition: background-color 0.3s ease, color 0.3s ease;
}

header.header-section a {
  color: var(--header-link-color);
  transition: color 0.3s ease;
  font-size:15px;
}

header.header-section a:hover {
  color: #ffd700; /* Optional hover color */
}

/* 🌗 Define color variables for light and dark mode */
:root {
  --bg-color: #ffffff;
  --text-color: #000000;

  --header-bg-color: #ffffff;
  --header-text-color: #000000;
  --header-link-color: #000000;

  --primary-bg: #f5f5f5;
  --primary-text: #111111;
}

body.dark-mode {
  --bg-color: #121212;
  --text-color: #eeeeee;

  --header-bg-color: #1a1a1a;
  --header-text-color: #ffffff;
  --header-link-color: #ffffff;

  --primary-bg: #222222;
  --primary-text: #f0f0f0;
}

/* 🌍 Base page colors */
/* 📦 Example content area using primary vars */
.some-div,
.black-text-div {
  background-color: var(--primary-bg);
  color: var(--primary-text);
  padding: 1rem;
  margin: 1rem 0;
  border-radius: 8px;
  transition: background-color 0.3s ease, color 0.3s ease;
}

/* 🌙 Dark Mode Toggle Button – KEEPING YOUR POSITION & SIZE */
.dark-mode-toggle {
  position: fixed;
  top:1rem;
  right: 1rem;
  background-color: rgba(255, 255, 255, 0.8);
  border-radius: 50%;
  padding: 0.5rem;
  cursor: pointer;
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.1);
  z-index: 9999; /* Keep it on top */
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 3.5rem;
  height: 3.5rem;
}
@media (max-width:580px){
 .dark-mode-toggle {
  position: fixed;
  top:4rem;
  right: 1rem;
  background-color: rgba(255, 255, 255, 0.8);
  border-radius: 50%;
  padding: 0.5rem;
  cursor: pointer;
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.1);
  z-index: 9999; /* Keep it on top */
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1.7rem;
  height: 1.7rem;
}
}

.dark-mode-toggle:hover {
  background-color: rgba(255, 255, 255, 1);
}

/* 🌗 Toggle Icon Styling */
.toggle-icon {
  width: 24px;
  height: 24px;
  pointer-events: none; /* So click hits the container */
  filter: invert(0); /* Normal mode */
  transition: filter 0.3s ease;
}

/* 🔘 Active Dark Mode Button & Icon */
.dark-mode .dark-mode-toggle {
  background-color: rgba(50, 50, 50, 0.8);
}

body.dark-mode .dark-mode-toggle:hover {
  background-color: rgba(50, 50, 50, 1);
}

body.dark-mode .toggle-icon {
  filter: invert(1); /* For visibility on dark background */
}
/* Target the h6 inside your logo */
header .logo h6 {
  color: var(--header-text-color);
  transition: color 0.3s ease;
}

/* 🌗 Dark Mode Styles for Header Menu and Dropdowns */
body.dark-mode .menu {
  background-color: var(--header-bg-color);
  color: var(--header-text-color);
}

body.dark-mode .section {
  background-color: var(--header-bg-color);
  color: var(--header-text-color);
}

body.dark-mode .section .container h2 {
 color: #fff;
}
body.dark-mode .menu li a {
  color: var(--header-link-color);
}


body.dark-mode .menu li a:hover {
  color: var(--yellow); /* Optional: highlight on hover */
}

body.dark-mode .submenu {
  background-color: var(--header-bg-color);
  color: var(--header-text-color);
  border: 1px solid var(--gray-medium); /* Optional border in dark mode */
}

body.dark-mode .submenu li a {
  color: var(--header-link-color);
}

body.dark-mode .submenu li a:hover {
  color: var(--yellow-dark);
}
.menu,
.submenu,
.menu li a {
  transition: background-color 0.3s ease, color 0.3s ease;
}
.lightdark-switch .switch-icon {
    display: none !important;
}

</style>

<body>
    <div class="preloader">
        <img src="Images2/logo1.png" alt="preloader icon">
    </div>
    <div class="dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode" role="button" tabindex="0">
  <img src="images/moon.svg" alt="Toggle dark mode icon" class="toggle-icon" />
</div>
    <div class="lightdark-switch">
        <span class="switch-btn" id="btnSwitch">
            <img src="images/moon.svg" alt="light-dark-switchbtn"
            class="swtich-icon">
        </span>
    </div>
    <header class="header-section bg-color-3">
        <div class="header-bottom">
            <div class="container">
                <div class="header-wrapper">
                   <div class="logo">
                       <a href="#"  style="display: flex; align-items: center; gap: 1px;">
                          <img src="Images2/logo1.png" alt="Logo" style="height: 70px; width: auto;">
                          <h6 style="margin: 0;">Global<br>Coinverse</h6>
                         </a>
                    </div>
                    <div class="menu-area">
                        <ul class="menu menu--style1">
                            <li class="">
                                <a href="index.php">Home </a>
                            </li>
                            <li class="">
                                <a href="about.php">About </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">Services</a>
                                <ul class="submenu">
                                    <li><a href="">Buy Crypto</a></li>
                                    <li><a href="login.php">Copy Trading</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)">Trading Tools</a>
                                <ul class="submenu">
                                    <li><a href="https://coinmarketcap.com/" target="_blank">Crypto Market</a></li>
                       
                                    <li><a href="https://s.tradingview.com/embed-widget/advanced-chart/?locale=en#%7B%22symbol%22%3A%22FX%3AEURUSD%22%2C%22interval%22%3A%22D%22%2C%22theme%22%3A%22light%22%2C%22style%22%3A%221%22%2C%22locale%22%3A%22en%22%2C%22toolbar_bg%22%3A%22f1f3f6%22%2C%22enable_publishing%22%3Afalse%2C%22allow_symbol_change%22%3Atrue%2C%22calendar%22%3Afalse%2C%22support_host%22%3A%22https%3A%2F%2Fwww.tradingview.com%22%7D" width="100%" height="500" frameborder="0" allowfullscreen>Forex Charts</a></li>
                                </ul>
                            </li>
                           
                            <li>
                                <a href="contact.php">Contact Us</a>
                            </li>
                            <li>
                                <a href="login.php">Log in</a>
                            </li>
                            <li>
                                <a href="registration.php">Sign up</a>
                            </li>
                        </ul>
                    </div>
                    <div class="header-action">
                        <div class="menu-area">
                            <!-- toggle icons -->
                            <div class="header-bar d-lg-none header-bar--style1">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pos">
            <div class="cryptohopper-web-widget" data-id="2" data-realtime="on"></div>
        </div>
    </header>
    <!-- ===============>> Header section end here <<================= --><title>Buy Crypto - Best Trading Platform </title>
  

  <!-- ================> Page header start here <================== -->
  	<section class="page-header bg--cover" style="background-image:url(Images2/asa-e-k-13EsoonqDkY-unsplash.jpg)">
  		<div class="container">
  			<div class="page-header__content pt-3" data-aos="fade-right" data-aos-duration="1000">
  				<h2 class="mt-5">About us</h2>
  				<nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
  					<ol class="breadcrumb mb-0">
  						<li class="breadcrumb-item "><a href="index.php">Home</a></li>
  						<li class="breadcrumb-item active" aria-current="page">Buy Crypto</li>
  					</ol>
  				</nav>
  			</div>
  			<div class="page-header__shape">
  				<span class="page-header__shape-item page-header__shape-item--1">
  				</div>
  			</div>
  		</section>
  		<!-- ================> Page header end here <================== -->
<!-- Start About -->
<!-- Start About -->
<section class="section">
    <div class="container pt-5">
        <h2 class="text-center pt-3 fw-bolder mb-4">Buy Crypto</h2>
        <p class="text-center" data-aos="fade-up" data-aos-duration="1000">Buy bitcoin, ethereum, and other crypto currencies for account funding from third parties.</p>

        <div class="ms-auto me-auto my-5">
        	<center>
            <a href="https://www.bitcoin.com/" target="_blank" class="card btn-primary shadow border-0 w-75 p-4 mb-4" data-aos="fade-up" data-aos-duration="1400">
        			<h5 class="fw-bold">Bitcoin</h5>
        		</a>
            <a href="https://www.moonpay.com/"target="_blank"  class="card btn-primary shadow border-0 w-75 p-4 mb-4" data-aos="fade-up" data-aos-duration="1300">
        			<h5 class="fw-bold">Moon Pay</h5>
        		</a>
        		<a href="https://paxful.com/" target="_blank" class="card btn-primary shadow border-0 w-75 p-4 mb-4" data-aos="fade-up" data-aos-duration="1100">
        			<h5 class="fw-bold">Paxful</h5>
        		</a>
            <a href="https://bitso.com/" target="_blank" class="card btn-primary shadow border-0 w-75 p-4 mb-4" data-aos="fade-up" data-aos-duration="1000">
        			<h5 class="fw-bold">Bitso</h5>
        		</a>
        		<a href="https://coinbase.com/" target="_blank" class="card btn-primary shadow border-0 w-75 p-4 mb-4" data-aos="fade-up" data-aos-duration="1200">
        			<h5 class="fw-bold">Coinbase</h5>
        		</a>
        		
        		
        	</center>
        </div>
    </div><!--end container-->
</section><!--end section-->
  <!-- ===============>> footer start here <<================= -->
  <footer class="footer ">
    <div class="container">
      <div class="footer__wrapper">
        <div class="footer__top footer__top--style1">
          <div class="row gy-5 gx-4">
            <div class="col-md-6">
              <div class="footer__about">
                <a href="index-2.html" class="footer__about-logo">
                 <h6 style="color:#fff">Global<br>Coinverse</h6>   </a>
                <p class="footer__about-moto ">We help your money grow by putting it to work. Not just by words. Our experts ensure not only that your funds are at work, but are put in carefully planned and strategically diversified trading and investment portfolio for risk management.</p>
              </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
              <div class="footer__links">
                <div class="footer__links-tittle">
                  <h6>Quick links</h6>
                </div>
                <div class="footer__links-content">
                  <ul class="footer__linklist">
                    <li class="footer__linklist-item"> <a href="index.php">Home</a>
                    </li>
					<li class="footer__linklist-item"> <a href="registration.php">Register</a>
                    <li class="footer__linklist-item"> <a href="https://coinmarketcap.com/"target="_blank">Crypto markets</a>
                    </li>
                    <li class="footer__linklist-item"> <a href="https://s.tradingview.com/embed-widget/advanced-chart/?locale=en#%7B%22symbol%22%3A%22FX%3AEURUSD%22%2C%22interval%22%3A%22D%22%2C%22theme%22%3A%22light%22%2C%22style%22%3A%221%22%2C%22locale%22%3A%22en%22%2C%22toolbar_bg%22%3A%22f1f3f6%22%2C%22enable_publishing%22%3Afalse%2C%22allow_symbol_change%22%3Atrue%2C%22calendar%22%3Afalse%2C%22support_host%22%3A%22https%3A%2F%2Fwww.tradingview.com%22%7D" width="100%" height="500" frameborder="0" allowfullscreen>Forex Charts</a> </li>
                    
                    </li>
                  </ul>
                </div>
              </div>

            </div>
            <div class="col-md-2 col-sm-4 col-6">
              <div class="footer__links">
                <div class="footer__links-tittle">
                  <h6>Support</h6>
                </div>
                <div class="footer__links-content">
                  <ul class="footer__linklist">
                    <li class="footer__linklist-item"> <a href="terms.php">Terms & Conditions</a>
                    </li>
                    <li class="footer__linklist-item"> <a href="privacy.php">Privacy Policy</a>
                    </li>
                    <li class="footer__linklist-item"> <a href="riskdisclosure.php">Risk Disclosure</a></li>
                    <li class="footer__linklist-item"> <a href="contact.html">Contact Us</a> </li>
                  </ul>
                </div>
              </div>

            </div>
            <div class="col-md-2 col-sm-4">
              <div class="footer__links">
                <div class="footer__links-tittle">
                  <h6>Company</h6>
                </div>
                <div class="footer__links-content">
                  <ul class="footer__linklist">
                    <li class="footer__linklist-item"> <a href="dashboard.php #copy-trading">Copy Trading</a>
                    </li>
                   <li class="footer__linklist-item"><a href="dashboard.php #investment-plans">Investment Plans</a></li>
                    </li>
                    <li class="footer__linklist-item"> <a href="regristration.php">Investments</a> </li>
                    <li class="footer__linklist-item"> <a href="registration.php">Crypto Trading</a>
                    </li>
                  </ul>
                </div>
              </div>

            </div>
          </div>
        </div>
        <div class="footer__bottom">
          <div class="footer__end">
            <div class="footer__end-copyright">
              <p class=" mb-0">Â© 2025 All Rights Reserved By Global Coinverse </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer__shape">
      <span class="footer__shape-item footer__shape-item--1"><img src="images/trade.png"
          alt="shape icon"></span>
      <span class="footer__shape-item footer__shape-item--2"> <span></span> </span>
    </div>
  </footer>
  <!-- ===============>> footer end here <<================= -->


  <!-- ===============>> scrollToTop start here <<================= -->
  <a href="#" class="scrollToTop scrollToTop--style1"><i class="fa-solid fa-arrow-up-from-bracket"></i></a>
  <!-- ===============>> scrollToTop ending here <<================= -->

  <!-- vendor plugins -->
  <script src="JS/jquery-3.4.1.min.js"></script>
  <script src="JS/bootstrap.bundle.min.js"></script>
  <script src="/JS/all.min.js"></script>
  <script src="JS/swiper-bundle.min.js"></script>
  <script src="JS/aos.js"></script>
  <script src="JS/toastr.js"></script>
  <script src="JS/fslightbox.js"></script>
  <script src="JS/purecounter_vanilla.js"></script>
<script src="https://www.cryptohopper.com/widgets/js/script"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Faker/3.1.0/faker.min.js"></script>
<script src="JS/custom.js"></script>
<script>window.gtranslateSettings = {"default_language":"en","wrapper_selector":".gtranslate_wrapper"}</script>
<script src="https://cdn.gtranslate.net/widgets/v1.0.1/float.js" defer></script>
<script type="text/javascript">
    setInterval(function(){
        let _info = faker.helpers.createCard();
        let {name, email, address:{city,country,zipcode}} = _info; 
        var _amount = (Math.random()*10000).toFixed(2);
        var _label_message = name+" just invested the sum of $"+_amount;
        toastr["info"](_label_message);
        
     }, 30000);

    setInterval(function(){
        let info = faker.helpers.createCard();
        let {name, email, address:{city,country,zipcode}} = info; 
        var amount = (Math.random()*100000).toFixed(2);
        var label_message = "<font color='white'>"+name+" from "+city+", "+country+" withdrew the sum of $"+amount+".</font>";
        var header_message = "<font color='white'>Recent Withdrawal!</font>";
        toastr["success"](label_message, header_message);
    }, 20000);
</script>
<script>
  const toggleBtn = document.getElementById('darkModeToggle');
toggleBtn.addEventListener('click', () => {
  document.body.classList.toggle('dark-mode');
});
</script>

</body>
</html>