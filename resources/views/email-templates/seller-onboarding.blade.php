<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to InteriorChowk</title>
    <style type="text/css">
            body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
                }
        
        .email-container {
           /* max-width: 600px;*/
            margin: 20px;
            padding: 50px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #0056b3;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        h2 {
            font-size: 20px;
            margin-top: 20px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        h3 {
            font-size: 18px;
            margin-top: 15px;
            color: #333;
            margin-bottom: 10px;
        }
        
        p {
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        p.greeting {
            font-size: 18px;
            font-weight: bold;
        }
        
        ul {
            margin-left: 20px;
            margin-bottom: 20px;
        }
        
        a {
            color: #0056b3;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        p.closing {
            font-weight: bold;
            margin-top: 20px;
        }
        
        p.social-links {
            text-align: center;
            margin-top: 20px;
        }
        
        p.social-links a {
            margin: 0 5px;
            display: inline-block;
            color: #0056b3;
        }
        
        p.social-links a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="email-container">
        <p class="greeting">Congratulations {{ $data->f_name }} {{ $data->l_name }},</p>
        <p>Your registration process has been successfully completed and your seller code is VN{{ $data->id }}.</p>
        <p>Welcome to InteriorChowk, where we are thrilled to have you on board as our newest seller. We appreciate the trust you've placed in us and are committed to making your experience with our services exceptional.</p>
        
        <h2>To ensure a smooth onboarding process, we have outlined the next steps below:</h2>
        
        <div class="section">
            <h3>Account Setup:</h3>
            <ul>
                <li>Visit our <a href="{{ route('seller.auth.seller-login') }}">InteriorChowk</a> and log in with your credentials.</li>
                <li>Complete your profile to personalize your experience.</li>
            </ul>
        </div>

        <div class="section">
            <h3>Getting Started Guide:</h3>
            <ul>
                <li><p>We've crafted an extensive guide to assist you in beginning your journey. You can access it through this link: <a href="{{asset('storage/seller_guide/IC%20Seller%E2%80%99s%20Guide.pdf')}}">Seller’s Guide</a></p></li>
            </ul>
            
        </div>

        <div class="section">
            <h3>Welcome Package:</h3>
            <ul><li><p>Keep an eye on your mailbox! We'll be sending you a welcome package with some exciting offers & surprises.</p></li></ul>
            
        </div>

        <div class="section">
            <h3>Training Sessions:</h3>
            <ul><li>
                <p>Enhance your experience with our complimentary training sessions. Let our expert team walk you through the essential features and address any queries you might have. <a href="{{ route('seller-chowk') }}#:~:text=Any,I%20am%3A%2D">Click here to schedule your session and get connected.</a></p>
            </li></ul>
            
        </div>

        <div class="section">
            <h3>Support and Resources:</h3>
            <ul><li>
                <p>Our customer support team is ready to assist you. Don't hesitate to reach out with any queries. Additionally, explore our online resources and knowledge base for helpful articles and tutorials.</p>
            </li></ul>
           
        </div>

        <div class="section">
            <h3>Feedback Matters:</h3>
            <ul><li>
                <p>Your feedback is invaluable to us. As you explore our offerings, we encourage you to share your thoughts and suggestions. We're constantly striving to improve and tailor our services to your needs.</p>
            </li></ul>
           
        </div>
                 <?php 
               $social_media=\App\Model\SocialMedia::all();
               
               ?>
        <div class="section">
            <h3>Join Seller community</h3>
            <p>Become a part of our seller community on <a href="{{ optional($social_media->firstWhere('name', 'instagram'))->link; }}">Instagram</a>, <a href="{{ optional($social_media->firstWhere('name', 'facebook'))->link; }}">Facebook</a>, and <a href="{{ optional($social_media->firstWhere('name', 'youtube'))->link; }}">YouTube</a>, and receive daily notifications about the latest updates.</p>
        </div>

        <p>Thank you again for choosing InteriorChowk. We are confident that our partnership will be mutually beneficial, and we look forward to being a part of your success.</p>

        <p>If you have any immediate questions or concerns, feel free to reply to this email or contact our support team at <a href="mailto:support@interiorchowk.com">support@interiorchowk.com</a> or call us on +91 9953 680 690</p>

        <p>Once again, welcome aboard!</p>
        
        <p class="closing">Best Regards,<br><br><br>Support Team<br><br><a href="{{ route('seller.auth.seller-login') }}">InteriorChowk</a></p>

        <p class="social-links">
            <a href="{{ url('/') }}">Website</a> |
            <a href="{{ optional($social_media->firstWhere('name', 'instagram'))->link; }}">Instagram</a> |
            <a href="{{ optional($social_media->firstWhere('name', 'facebook'))->link; }}">Facebook</a> |
            <a href="{{ optional($social_media->firstWhere('name', 'youtube'))->link; }}">YouTube</a>
        </p>
    </div>
</body>
</html>
