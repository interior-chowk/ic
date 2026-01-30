<!-- Seller onboarding mail -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Newsletter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1F4386;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #1F4386;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            position: relative;
        }

        .header img {
            max-height: 40px;
        }

        .header a {
            padding: 10px 20px;
            background-color: #E26525;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
        }

        .main-image {
            width: 100%;
            height: auto;
        }

        .content {
            padding: 20px;
            text-align: left;
        }

        .content h1 {
            color: #1F4386;
        }

        .content p {
            color: #333333;
            line-height: 1.6;
        }

        .content .highlight {
            color: #1F4386;
            font-weight: bold;
        }

        .content .button {
            display: inline-block;
            padding: 10px 15px;
            margin: 50px 0;
            background-color: #E26525;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 18px;
            text-align: center;
        }

        .footer {
            background-color: #ffffff;
            color: #333333;
            text-align: center;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }

        .footer a {
            color: #1F4386;
            text-decoration: none;
        }

        .footer .social-icons {
            margin: 20px 0;
        }

        .footer .social-icons img {
            max-height: 30px;
            margin: 0 10px;
        }

        .footer .small {
            font-size: 12px;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header a {
                margin-top: 10px;
                padding: 10px 20px;
            }

            .content .button {
                display: block;
                width: 100%;
                text-align: center;
            }

            .footer .social-icons {
                display: flex;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="http://cdn.mcauto-images-production.sendgrid.net/4285993ee8562336/29d2cef6-e9c5-4994-a4e9-e2711fa5bc80/210x70.png"
                alt="InteriorChowk Logo">
            <a href="https://interiorchowk.com/storage/app/public/seller_guide/IC%20Seller%E2%80%99s%20Guide.pdf">Seller
                Guide</a>
        </div>
        <img src="http://cdn.mcauto-images-production.sendgrid.net/4285993ee8562336/e821bd1b-290a-42a8-b640-5a04fda6f0ae/884x554.png"
            alt="Invitation" class="main-image">
        <div class="content">
            <p>Dear <span class="highlight">{{ $seller->f_name }} {{ $seller->l_name }}</span>,</p>
            <p>I hope this email finds you well. We are reaching out to you because we admire the products you offer and
                believe they would be a great fit for our platform.</p>
            <h1>InteriorChowk, India's first dedicated marketplace</h1>
            <p>for home interior buyers. It is a mobile app where a multitude of sellers, interior designers,
                architects, contractors, workers, and many more.</p>
            <p>By joining InteriorChowk as a seller, you will gain access to a dedicated marketplace that prioritizes
                the needs of both sellers and buyers.</p>
            <h2>Our platform offers:</h2>
            <ul>
                <li><span class="highlight">Selling at minimum Commission*</span>: Sell your products with the minimum
                    commission*</li>
                <li><span class="highlight">Increased Exposure</span>: Showcase your products to a targeted audience of
                    home interior enthusiasts across India.</li>
                <li><span class="highlight">Free branding & promotion*</span>: InteriorChowk is committed to supporting
                    your growth journey, and our free branding and promotion services aim to boost your presence in the
                    competitive market.</li>
                <li><span class="highlight">Seamless Integration</span>: Our user-friendly seller dashboard makes it
                    easy to upload and manage your inventory, track sales, and engage with customers.</li>
                <li><span class="highlight">Dedicated Support</span>: Our team is committed to providing personalized
                    assistance to ensure that your selling experience is smooth and successful.</li>
                <li><span class="highlight">Zero waiting for payments</span>: Receive your payment instantly once we
                    have received it from our payment gateway.</li>
                <li><span class="highlight">One stop solution</span>: InteriorChowk is not just an app; it's a platform
                    where a multitude of architects, interior designers, and contractors actively recommend your
                    products to their customers.</li>
            </ul>
            <p>We are excited about the opportunity to collaborate with you and showcase your exceptional products on
                InteriorChowk.</p>
            <p>Warm regards,<br>Support Team<br>InteriorChowk</p>
            <p><a href="mailto:support@interiorchowk.com">support@interiorchowk.com</a><br>+91 9953 680 690</p>
            <a href="https://interiorchowk.com/seller/auth/seller-registeration" class="button">Register Now</a>
        </div>
        <div class="footer">
            <div class="social-icons">
                <a href="https://www.facebook.com/profile.php?id=61557449746068"><img
                        src="http://cdn.mcauto-images-production.sendgrid.net/4285993ee8562336/0510463a-7b42-4cc2-a050-35b9483853b6/71x71.png"
                        alt="Facebook"></a>
                <a href="https://www.instagram.com/icsellerchowk/"><img
                        src="http://cdn.mcauto-images-production.sendgrid.net/4285993ee8562336/7cc59e38-ad58-4b82-9659-d1cbd504f5eb/71x71.png"
                        alt="Instagram"></a>
                <a href="https://www.youtube.com/channel/UCn2inp-QlGEjgtl02CG1iWg"><img
                        src="http://cdn.mcauto-images-production.sendgrid.net/4285993ee8562336/37bf0133-d4ca-4dc1-bd8a-ebf685335690/71x71.png"
                        alt="YouTube"></a>
                <a href="https://www.linkedin.com/company/100782897/admin/feed/posts/"><img
                        src="http://cdn.mcauto-images-production.sendgrid.net/4285993ee8562336/c8f2837d-8463-4ad0-9534-208185fbda1d/71x71.png"
                        alt="LinkedIn"></a>
            </div>
            <p>&copy; 2024 Soham Infratech. All rights reserved.</p>
            <p class="small"><br>You can <a href="#">update your preferences</a> or <a
                    href="#">unsubscribe</a>.</p>
        </div>
    </div>
</body>

</html>
