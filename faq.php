<?php
$page_title = 'FAQ';
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

$faqs = [
    [
        'question' => 'What payment methods do you accept?',
        'answer' => 'We accept Cash on Delivery (COD) for all orders. Currently, we do not support online payment gateways.'
    ],
    [
        'question' => 'How long does delivery take?',
        'answer' => 'Delivery typically takes 3-5 business days within major cities. Rural areas may take 5-7 business days.'
    ],
    [
        'question' => 'Do you offer product warranty?',
        'answer' => 'Yes, all products come with a 6-month warranty against manufacturing defects. Please keep your order receipt.'
    ],
    [
        'question' => 'Can I cancel my order?',
        'answer' => 'Orders can be cancelled within 24 hours of placing the order. Please contact our support team for assistance.'
    ],
    [
        'question' => 'Do you ship internationally?',
        'answer' => 'Currently, we only ship within Pakistan. We plan to expand internationally in the future.'
    ],
    [
        'question' => 'How do I track my order?',
        'answer' => 'You can track your order by logging into your account and visiting the "My Orders" section.'
    ],
    [
        'question' => 'What if I receive a damaged product?',
        'answer' => 'If you receive a damaged product, please contact us within 48 hours with photos of the damage. We will arrange a replacement.'
    ],
    [
        'question' => 'Do you have a physical store?',
        'answer' => 'We are currently an online-only store. However, we are planning to open physical stores in major cities soon.'
    ]
];
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5" data-aos="fade-up">
                <h1 class="display-4 fw-bold gaming-logo">Frequently Asked Questions</h1>
                <p class="text-muted">Find answers to common questions about our products and services</p>
            </div>
            
            <div class="faq-container" data-aos="fade-up">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question" data-index="<?php echo $index; ?>">
                            <span><?php echo htmlspecialchars($faq['question']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.faq-item {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-item:hover {
    border-color: #6c5ce7;
}

.faq-question {
    padding: 20px 25px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    color: var(--text-primary);
    transition: all 0.3s ease;
}

.faq-question:hover {
    background: rgba(108, 92, 231, 0.05);
}

.faq-question i {
    transition: transform 0.3s ease;
    color: #6c5ce7;
}

.faq-question.active i {
    transform: rotate(180deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.3s ease;
    padding: 0 25px;
}

.faq-answer.open {
    max-height: 300px;
    padding: 0 25px 20px;
}

.faq-answer p {
    color: var(--text-secondary);
    line-height: 1.8;
    margin: 0;
}
</style>

<script>
document.querySelectorAll('.faq-question').forEach(item => {
    item.addEventListener('click', function() {
        const answer = this.nextElementSibling;
        const icon = this.querySelector('i');
        
        // Close all others
        document.querySelectorAll('.faq-answer').forEach(a => {
            if (a !== answer) {
                a.classList.remove('open');
                a.previousElementSibling.classList.remove('active');
            }
        });
        
        // Toggle current
        answer.classList.toggle('open');
        this.classList.toggle('active');
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>