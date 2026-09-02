<?php
require_once 'config/database.php';
include 'includes/header.php';
?>

<!-- Page Header -->

<div style="padding:20px 20px 80px;max-width:700px;margin:0 auto;">

    <!-- Brand Story -->
    <div style="background:var(--white);border-radius:var(--radius);padding:28px 24px;box-shadow:var(--shadow-light);margin-bottom:20px;">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:80px;height:80px;border-radius:50%;background:var(--beige);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <i class="fas fa-mug-hot" style="font-size:36px;color:var(--gold);"></i>
            </div>
            <h2 style="font-size:22px;font-weight:700;color:var(--primary-dark);">يوفيا | UVIA</h2>
            <p style="color:var(--gold);font-size:14px;font-weight:500;">☕ Specialty Coffee</p>
        </div>
        
        <div style="border-top:2px solid var(--beige);padding-top:16px;">
            <p style="font-size:15px;line-height:1.9;color:var(--text-muted);text-align:center;">
                في <strong style="color:var(--primary-dark);">يوفيا</strong>، نؤمن أن القهوة ليست مجرد مشروب، 
                بل هي تجربة تُحضّر بعناية لتتناسب مع مزاجك. نختار أجود حبوب البن من أفضل المزارع حول العالم، 
                ونُحضّرها بحب واحترافية لنقدم لك كوباً استثنائياً في كل مرة.
            </p>
        </div>
    </div>

    <!-- Our Values -->
    <div style="background:var(--white);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow-light);margin-bottom:20px;">
        <h3 style="font-size:18px;font-weight:700;color:var(--primary-dark);text-align:center;margin-bottom:16px;">
            <i class="fas fa-heart" style="color:var(--gold);margin-left:8px;"></i>
            قيمنا
        </h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="background:var(--beige);border-radius:var(--radius-sm);padding:16px;text-align:center;">
                <i class="fas fa-seedling" style="font-size:24px;color:var(--gold);margin-bottom:6px;"></i>
                <h4 style="font-size:14px;font-weight:600;color:var(--primary-dark);">الجودة</h4>
                <p style="font-size:12px;color:var(--text-muted);">نختار أفضل الحبوب</p>
            </div>
            <div style="background:var(--beige);border-radius:var(--radius-sm);padding:16px;text-align:center;">
                <i class="fas fa-hand-holding-heart" style="font-size:24px;color:var(--gold);margin-bottom:6px;"></i>
                <h4 style="font-size:14px;font-weight:600;color:var(--primary-dark);">الشغف</h4>
                <p style="font-size:12px;color:var(--text-muted);">نُحضّر بحب</p>
            </div>
            <div style="background:var(--beige);border-radius:var(--radius-sm);padding:16px;text-align:center;">
                <i class="fas fa-leaf" style="font-size:24px;color:var(--gold);margin-bottom:6px;"></i>
                <h4 style="font-size:14px;font-weight:600;color:var(--primary-dark);">الاستدامة</h4>
                <p style="font-size:12px;color:var(--text-muted);">مصادر مسؤولة</p>
            </div>
            <div style="background:var(--beige);border-radius:var(--radius-sm);padding:16px;text-align:center;">
                <i class="fas fa-mug-saucer" style="font-size:24px;color:var(--gold);margin-bottom:6px;"></i>
                <h4 style="font-size:14px;font-weight:600;color:var(--primary-dark);">التجربة</h4>
                <p style="font-size:12px;color:var(--text-muted);">قهوة لمزاجك</p>
            </div>
        </div>
    </div>

    <!-- Coffee Philosophy -->
    <div style="background:var(--white);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow-light);margin-bottom:20px;">
        <h3 style="font-size:18px;font-weight:700;color:var(--primary-dark);text-align:center;margin-bottom:12px;">
            <i class="fas fa-quote-right" style="color:var(--gold);margin-left:8px;"></i>
            فلسفتنا في القهوة
        </h3>
        <div style="text-align:center;padding:12px 0;">
            <p style="font-size:28px;color:var(--gold);font-weight:300;line-height:1.6;">
                "القهوة الجيدة <br>تتحدث عن نفسها"
            </p>
            <p style="font-size:14px;color:var(--text-muted);margin-top:8px;">
                نؤمن بأن كل حبة بن تحكي قصة، ونحن هنا لنرويها لك.
            </p>
        </div>
    </div>

    <!-- Our Coffee Selection -->
    <div style="background:var(--white);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow-light);margin-bottom:20px;">
        <h3 style="font-size:18px;font-weight:700;color:var(--primary-dark);text-align:center;margin-bottom:12px;">
            <i class="fas fa-coffee" style="color:var(--gold);margin-left:8px;"></i>
            تشكيلتنا
        </h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:14px;color:var(--text-muted);">
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;">
                <i class="fas fa-check-circle" style="color:var(--gold);font-size:14px;"></i>
                <span>قهوة ساخنة</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;">
                <i class="fas fa-check-circle" style="color:var(--gold);font-size:14px;"></i>
                <span>قهوة باردة</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;">
                <i class="fas fa-check-circle" style="color:var(--gold);font-size:14px;"></i>
                <span>V60 مختص</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;">
                <i class="fas fa-check-circle" style="color:var(--gold);font-size:14px;"></i>
                <span>مشروبات</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;">
                <i class="fas fa-check-circle" style="color:var(--gold);font-size:14px;"></i>
                <span>حلويات</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;">
                <i class="fas fa-check-circle" style="color:var(--gold);font-size:14px;"></i>
                <span>إضافات</span>
            </div>
        </div>
    </div>

    <!-- Location / Contact -->
    <div style="background:var(--white);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow-light);margin-bottom:20px;">
        <h3 style="font-size:18px;font-weight:700;color:var(--primary-dark);text-align:center;margin-bottom:12px;">
            <i class="fas fa-location-dot" style="color:var(--gold);margin-left:8px;"></i>
            تفضل بزيارتنا
        </h3>
        <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;color:var(--text-muted);">
            <div style="display:flex;align-items:center;gap:12px;padding:8px 12px;background:var(--beige);border-radius:var(--radius-sm);">
                <i class="fas fa-map-pin" style="color:var(--gold);width:20px;"></i>
                <span>شارع القهوة، حي العليا الرياض</span>
            </div>
            <div style="display:flex;align-items:center;gap:12px;padding:8px 12px;background:var(--beige);border-radius:var(--radius-sm);">
                <i class="fas fa-clock" style="color:var(--gold);width:20px;"></i>
                <span>٧:٠٠ صباحاً - ١٢:٠٠ منتصف الليل</span>
            </div>
            <div style="display:flex;align-items:center;gap:12px;padding:8px 12px;background:var(--beige);border-radius:var(--radius-sm);">
                <i class="fas fa-phone" style="color:var(--gold);width:20px;"></i>
                <span>+966 50 000 0000</span>
            </div>
            <div style="display:flex;align-items:center;gap:12px;padding:8px 12px;background:var(--beige);border-radius:var(--radius-sm);">
                <i class="fas fa-envelope" style="color:var(--gold);width:20px;"></i>
                <span>hello@uvia.coffee</span>
            </div>
        </div>
    </div>

    <!-- Social Links -->
    <div style="background:var(--white);border-radius:var(--radius);padding:20px 24px;box-shadow:var(--shadow-light);text-align:center;">
        <h3 style="font-size:16px;font-weight:700;color:var(--primary-dark);margin-bottom:12px;">
            تابعنا
        </h3>
        <div style="display:flex;justify-content:center;gap:16px;">
            <a href="#" style="width:48px;height:48px;border-radius:50%;background:var(--beige);display:flex;align-items:center;justify-content:center;color:var(--text-muted);text-decoration:none;transition:var(--transition);font-size:20px;">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="#" style="width:48px;height:48px;border-radius:50%;background:var(--beige);display:flex;align-items:center;justify-content:center;color:var(--text-muted);text-decoration:none;transition:var(--transition);font-size:20px;">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="#" style="width:48px;height:48px;border-radius:50%;background:var(--beige);display:flex;align-items:center;justify-content:center;color:var(--text-muted);text-decoration:none;transition:var(--transition);font-size:20px;">
                <i class="fab fa-snapchat"></i>
            </a>
            <a href="#" style="width:48px;height:48px;border-radius:50%;background:var(--beige);display:flex;align-items:center;justify-content:center;color:var(--text-muted);text-decoration:none;transition:var(--transition);font-size:20px;">
                <i class="fab fa-tiktok"></i>
            </a>
        </div>
    </div>

    <!-- Back Button -->
    <div style="text-align:center;margin-top:20px;">
        <a href="index.php" class="btn-secondary" style="font-size:14px;">
            <i class="fas fa-arrow-right"></i> العودة للرئيسية
        </a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>