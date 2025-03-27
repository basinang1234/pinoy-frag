<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>About Us</h3>
            <p>Fragrance Haven is your ultimate destination for perfume enthusiasts, offering community discussions, expert reviews, and fragrance comparisons.</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-pinterest"></i></a>
            </div>
        </div>
        
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="perfumes.php">Perfumes</a></li>
                <li><a href="perfumers.php">Perfumers</a></li>
                <li><a href="forums.php">Forums</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Newsletter</h3>
            <p>Subscribe for fragrance tips, new releases, and exclusive offers:</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2025 Fragrance Haven. All rights reserved.</p>
        <div class="legal-links">
            <a href="privacy-policy.php">Privacy Policy</a>
            <a href="terms.php">Terms of Service</a>
        </div>
    </div>
</footer>


<style>
.site-footer {
    background: #1877F2;
    color: white;
    padding: 40px 20px 20px;
    font-size: 14px;
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.footer-section {
    flex: 1 1 200px;
    margin-bottom: 20px;
}

.footer-section h3 {
    font-size: 18px;
    margin-bottom: 15px;
    border-bottom: 2px solid white;
    padding-bottom: 5px;
}

.social-icons a {
    color: white;
    margin-right: 15px;
    font-size: 16px;
    transition: transform 0.2s;
}

.social-icons a:hover {
    transform: scale(1.2);
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section li {
    margin: 8px 0;
}

.newsletter-form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.newsletter-form input {
    padding: 8px;
    flex: 1;
    border: none;
    border-radius: 4px;
}

.newsletter-form button {
    padding: 8px 20px;
    background: #0D47A1;
    border: none;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
}

.newsletter-form button:hover {
    background: #0A3B8C;
}

.footer-bottom {
    border-top: 1px solid #333;
    padding-top: 20px;
    margin-top: 30px;
    text-align: center;
}

.legal-links {
    margin-top: 10px;
}

.legal-links a {
    color: white;
    margin: 0 10px;
    text-decoration: none;
}

@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        align-items: center;
    }
    
    .footer-section {
        text-align: center;
    }
    
    .social-icons {
        justify-content: center;
    }
}
</style>

