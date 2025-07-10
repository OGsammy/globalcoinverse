<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	

    <!-- mario pro  meta Data -->
    <meta name="application-name"
    content="Mario Pro coins- Best Platform for Your Crypto, Forex, Stocks & Day Trading">
    <meta name="author" content="Mario pro">
    <meta name="keywords" content="Mario pro coin, Crypto, Forex, and Stocks Trading Business, Copy Trading">
    <meta name="description"
    content="Experience the power of copy trading, the ultimate platform designed to transform trading. With its sleek design and advanced features, Coin Wealth Pro empowers you to showcase your expertise, engage in trades, and dominate the markets. Elevate your online presence and unlock new trading possibilities with Coin Wealth Pro.">

    <!-- OG meta data -->
    <meta property="og:title"
    content="Mario pro coin - Best Online Platfotm for Your Crypto, Forex, Stocks & Day Trading">
    <meta property="og:Coin Mario Pro_name" content=Coin Wealth Pro>
    <meta property="og:url" content="index">
    <meta property="og:description"
    content="Welcome to Mario Pro coin, the game-changing platform meticulously crafted to revolutionize trading business. With its sleek and modern design, Coin Wealth Pro provides a cutting-edge platform to showcase your expertise, attract good profits, and stay ahead in the competitive trading markets.">
    <meta property="og:type" content="webCoin Wealth Pro">
    <meta property="og:image" content="images/favicon.png">

    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="CSS/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/aos.css">
    <link rel="stylesheet" href="CSS/toastr.css">
    <link rel="stylesheet" href="CSS/all.min.css">

    <link rel="stylesheet" href="CSS/swiper-bundle.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- main css for template -->
    <link rel="stylesheet" href="CSS/style.css">
</head>
<style>
    .pos{
     z-index: 1;
     position: fixed !important;
     top: 82px;
 }
 

  /* Base Colors */
  :root {
    --yellow: #f1c40f;
    --yellow-dark: #d4ac0d;
    --white: #fff;
    --black: #111;
    --gray-light: #f9f9f9;
    --gray-medium: #666;
    --gray-dark: #333;
    --error-red: #e74c3c;
  }

  /* Dark mode overrides */
  body.dark-mode {
    --yellow: #f7dc6f;
    --yellow-dark: #d4ac0d;
    --white: #1f1f1f;
    --black: #eee;
    --gray-light: #2c2c2c;
    --gray-medium: #bbb;
    --gray-dark: #ddd;
  }

  .contact-section {
    padding: 60px 20px;
    background: var(--gray-light);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: var(--gray-dark);
    transition: background 0.3s ease, color 0.3s ease;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: var(--gray-medium);
    transition: 0.4s;
    border-radius: 24px;
  }
  .slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: var(--white);
    transition: 0.4s;
    border-radius: 50%;
  }
  input:checked + .slider {
    background-color: var(--yellow);
  }
  input:checked + .slider:before {
    transform: translateX(21px);
  }

  .contact-container {
    max-width: 700px;
    margin: 0 auto;
    background: var(--white);
    border-radius: 12px;
    padding: 45px 50px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    transition: background 0.3s ease, color 0.3s ease;
  }
  

  .contact-container h2 {
    font-size: 2.4rem;
    margin-bottom: 8px;
    color: var(--black);
  }
 

  .contact-container p {
    text-align: center;
    margin-bottom: 35px;
    font-size: 1.1rem;
    color: var(--gray-medium);
  }
  

  .contact-form .form-group {
    margin-bottom: 24px;
  }

  .contact-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--gray-dark);
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.05em;
  }
  

  .contact-form input,
  .contact-form textarea {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid var(--gray-medium);
    border-radius: 8px;
    font-size: 1rem;
    font-family: inherit;
    color: var(--gray-dark);
    background: var(--white);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    resize: vertical;
  }
  
  .contact-form input:focus,
  .contact-form textarea:focus {
    border-color: var(--yellow);
    outline: none;
    box-shadow: 0 0 10px var(--yellow);
  }

  .contact-btn {
    display: inline-block;
    background: var(--yellow);
    color: var(--black);
    font-weight: 700;
    padding: 14px 30px;
    border: none;
    border-radius: 10px;
    font-size: 1.15rem;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 14px rgba(241, 196, 15, 0.5);
  }
  .contact-btn:hover,
  .contact-btn:focus {
    background: var(--yellow-dark);
    color: var(--white);
    box-shadow: 0 6px 20px rgba(212, 172, 13, 0.8);
  }

  /* Contact Info Section */
  .contact-info {
    margin-top: 40px;
    border-top: 2px solid var(--yellow);
    padding-top: 30px;
    font-size: 1.05rem;
    color: var(--gray-dark);
  }
 
  .contact-info h3 {
    font-weight: 700;
    margin-bottom: 15px;
    color: var(--black);
  }
  
  .contact-info a {
    color: var(--yellow-dark);
    text-decoration: none;
    transition: color 0.3s ease;
  }
  .contact-info a:hover,
  .contact-info a:focus {
    color: var(--yellow);
    text-decoration: underline;
  }

  

  /* Responsive */
  @media (max-width: 720px) {
    .contact-container {
      padding: 30px 20px;
    }
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
body {
  background-color: var(--bg-color);
  color: var(--text-color);
  font-family: Arial, sans-serif;
  transition: background-color 0.3s ease, color 0.3s ease;
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
  width: 2rem;
  height: 2rem;
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
body.dark-mode .dark-mode-toggle {
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

#formMessage {
  animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
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



    <header class="header-section header-section--style2">
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
                                    <li><a href=".about.html">purchase Crypto</a></li>
                                    <li><a href="login.php">Copy Trading</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)">Trading</a>
                                <ul class="submenu"><li><a href=".https://coinmarketcap.com/">Crypto Market</a></li>
                                   
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
                                <a href="registration.php">register</a>
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
    <!-- ===============>> Header section end here <<================= --><title> MARIO PRO - Best Trading Platform </title>

    
   <br><br><br><br>
<section class="hero-header" style="background-image: url('images/behnam-norouzi-RDXcFY5g5O4-unsplash.jpg'); background-size: cover; background-position: center; padding: 120px 0; position: relative;">
  <!-- Dark overlay -->
  <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 1;"></div>

  <!-- Text content -->
  <div style="position: relative; z-index: 2; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
    <h1 style="color: white; font-size: 48px; font-weight: bold; margin-bottom: 10px;">Contact Us</h1>
    <nav aria-label="breadcrumb">
      <ol style="display: flex; list-style: none; padding: 0; margin: 0; color: #fff;">
        <li><a href="index.html" style="color: orange; text-decoration: none; font-weight: 600;">Home</a></li>
        <li style="margin: 0 8px;">/</li>
        <li style="color: #ccc;">Contact</li>
      </ol>
    </nav>
  </div>
</section>



<!-- ===== Contact Us Section ===== -->
<br><br><br><section class="contact-section">
  <div class="contact-container">
    <h2 class="text-center">Get in Touch</h2>
    <p class="text-center subtitle">Our team is here to help you with any inquiries or support requests.</p>

    <form id="contactForm" class="contact-form" novalidate>
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required placeholder="Enter Your Name" />
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required placeholder="Enter Your Email Address" />
      </div>

      <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" required placeholder="Reason for contact" />
      </div>

      <div class="form-group">
        <label for="message">Your Message</label>
        <textarea id="message" name="message" rows="6" required placeholder="Type your message here..."></textarea>
      </div>

      <button type="submit" class="contact-btn" id="submitBtn">Send Message</button>
    </form>

   <!-- Success Message -->
<div id="formMessage" style="display:none; margin-top: 20px; color: green; font-weight: bold; text-align: center;">
  ✅ Message sent successfully!
</div>

<!-- Error Message -->
<div id="formError" style="display:none; margin-top: 20px; color: red; font-weight: bold; text-align: center;">
  ❌ Please fill in all fields.
</div>

<script>
  const contactForm = document.getElementById('contactForm');
  const formMessage = document.getElementById('formMessage');
  const formError = document.getElementById('formError');

  contactForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Get values
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();

    // Check if all fields are filled
    if (!name || !email || !subject || !message) {
      formError.style.display = 'block';
      formMessage.style.display = 'none';
      return;
    }

    // All good: show success, hide error
    formError.style.display = 'none';

    setTimeout(() => {
      formMessage.style.display = 'block';
      contactForm.reset();
    }, 300);
  });
</script>


    

   
</section>

<!-- ===============>> footer start here <<================= -->
  <footer class="footer ">
    <div class="container">
      <div class="footer__wrapper">
        <div class="footer__top footer__top--style1">
          <div class="row gy-5 gx-4">
            <div class="col-md-6">
              <div class="footer__about">
                <a href="index-2.html" class="footer__about-logo">
                 <h6 style="color:#fff"> Global<br>Coinverse</h6>   </a>
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
                    <li class="footer__linklist-item"> <a href="about.html">About Us</a>
                    </li>
                    <li class="footer__linklist-item"> <a href="registration.php">Register</a>
                    </li>
                   <li class="footer__linklist-item"> <a href="https://coinmarketcap.com/"target="_blank">Crypto markets</li></a>
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
                    <li class="footer__linklist-item"> <a href="tems.php">Terms & Conditions</a>
                    </li>
                    <li class="footer__linklist-item"> <a href="privacy.php">Privacy Policy</a>
                    </li>
                    <li class="footer__linklist-item"> <a href="riskdisclosure.php">Risk Disclosure</a></li>
                    <li class="footer__linklist-item"> <a href="contact.html">Support Center</a> </li>
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
                    <li class="footer__linklist-item"> <a href="registration.php">Copy Trading</a>
                    </li>
                    <li class="footer__linklist-item"> <a href="registration.php">Stock Trading</a>
                    </li>
                    <li class="footer__linklist-item"> <a href="registration.php">Forex Trading</a> </li>
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
              <p class=" mb-0">© 2025 All Rights Reserved By Global Coinverse </p>
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

<!-- ===== Styles ===== -->
<!-- ===== Scripts ===== -->




<script>
  const toggleBtn = document.getElementById('darkModeToggle');
toggleBtn.addEventListener('click', () => {
  document.body.classList.toggle('dark-mode');
});
</script>



</body>
</html>

<!-- ===== Styles ===== -->
<!-- ===== Scripts ===== -->
<script>
 
  


