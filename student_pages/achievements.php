<?php
/**
 * KIDSS - Achievements Page
 * Frontend-only for now; ready for later database integration.
 *
 * FUTURE BACKEND:
 * Replace $achievementData with database results.
 * Suggested fields:
 * achievement_id, code, title, description, icon, category,
 * target_value, progress, unlocked, unlocked_at, student_id.
 */

$achievementData = [
    ['code'=>'first-step','title'=>'First Step','description'=>'Complete your first lesson.','icon'=>'bi-stars','category'=>'Learning','progress'=>1,'target'=>1,'unlocked'=>true],
    ['code'=>'alphabet-starter','title'=>'Alphabet Starter','description'=>'Learn your first 5 ASL letters.','icon'=>'bi-alphabet-uppercase','category'=>'Learning','progress'=>5,'target'=>5,'unlocked'=>true],
    ['code'=>'alphabet-master','title'=>'Alphabet Master','description'=>'Complete all 26 alphabet lessons.','icon'=>'bi-book-fill','category'=>'Learning','progress'=>8,'target'=>26,'unlocked'=>false],
    ['code'=>'quiz-beginner','title'=>'Quiz Beginner','description'=>'Complete your first quiz.','icon'=>'bi-patch-question-fill','category'=>'Quiz','progress'=>1,'target'=>1,'unlocked'=>true],
    ['code'=>'perfect-score','title'=>'Perfect Score','description'=>'Get 100% on a quiz.','icon'=>'bi-trophy-fill','category'=>'Quiz','progress'=>0,'target'=>1,'unlocked'=>false],
    ['code'=>'quiz-champion','title'=>'Quiz Champion','description'=>'Complete 10 quizzes.','icon'=>'bi-award-fill','category'=>'Quiz','progress'=>3,'target'=>10,'unlocked'=>false],
    ['code'=>'practice-hero','title'=>'Practice Hero','description'=>'Practice sign language for 7 days.','icon'=>'bi-calendar-check-fill','category'=>'Practice','progress'=>4,'target'=>7,'unlocked'=>false],
    ['code'=>'word-explorer','title'=>'Word Explorer','description'=>'Complete your first 10 vocabulary words.','icon'=>'bi-chat-square-heart-fill','category'=>'Vocabulary','progress'=>10,'target'=>10,'unlocked'=>true],
    ['code'=>'vocabulary-builder','title'=>'Vocabulary Builder','description'=>'Learn 25 sign-language vocabulary words.','icon'=>'bi-bookmark-star-fill','category'=>'Vocabulary','progress'=>15,'target'=>25,'unlocked'=>false],
    ['code'=>'dedicated-learner','title'=>'Dedicated Learner','description'=>'Complete 20 lessons.','icon'=>'bi-lightning-charge-fill','category'=>'Learning','progress'=>12,'target'=>20,'unlocked'=>false]
];

$totalAchievements = count($achievementData);
$unlockedAchievements = count(array_filter($achievementData, fn($a) => $a['unlocked']));
$achievementPercentage = $totalAchievements ? round(($unlockedAchievements / $totalAchievements) * 100) : 0;
?>

<style>
.achievement-page{width:100%;padding:10px 4px 30px}
.achievement-header{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:22px;flex-wrap:wrap}
.achievement-title{margin:0;font-size:30px;font-weight:800;color:#1f2937}
.achievement-subtitle{margin:5px 0 0;color:#6b7280;font-size:14px}
.achievement-summary{min-width:190px;padding:15px 18px;border-radius:16px;background:#fff;border:1px solid #e5e7eb;box-shadow:0 5px 18px rgba(0,0,0,.05)}
.achievement-summary-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:9px;font-size:13px;color:#6b7280}
.achievement-summary-count{font-size:17px;font-weight:800;color:#374151}
.achievement-progress{width:100%;height:8px;overflow:hidden;border-radius:999px;background:#e5e7eb}
.achievement-progress-bar{height:100%;width:<?php echo $achievementPercentage; ?>%;border-radius:inherit;background:#6366f1}
.achievement-filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.achievement-filter{border:1px solid #e5e7eb;background:#fff;color:#6b7280;border-radius:999px;padding:8px 15px;font-size:13px;font-weight:700;cursor:pointer;transition:.2s ease}
.achievement-filter:hover,.achievement-filter.active{background:#6366f1;color:#fff;border-color:#6366f1}
.achievement-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
.achievement-card{position:relative;padding:20px;min-height:225px;border-radius:20px;background:#fff;border:1px solid #e5e7eb;box-shadow:0 6px 20px rgba(0,0,0,.045);transition:.2s ease}
.achievement-card:hover{transform:translateY(-3px);box-shadow:0 10px 25px rgba(0,0,0,.08)}
.achievement-card.locked{opacity:.78}
.achievement-icon{width:55px;height:55px;display:flex;align-items:center;justify-content:center;border-radius:16px;margin-bottom:14px;background:#eef2ff;color:#6366f1;font-size:25px}
.achievement-card.locked .achievement-icon{background:#f3f4f6;color:#9ca3af}
.achievement-status{position:absolute;top:18px;right:18px;padding:5px 9px;border-radius:999px;font-size:11px;font-weight:800;background:#ecfdf5;color:#059669}
.achievement-status.locked{background:#f3f4f6;color:#6b7280}
.achievement-card h3{margin:0 0 7px;color:#1f2937;font-size:18px;font-weight:800}
.achievement-card p{margin:0 0 17px;color:#6b7280;font-size:13px;line-height:1.55}
.achievement-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;font-size:12px;color:#6b7280}
.achievement-meta strong{color:#374151}
.achievement-card-progress{height:7px;border-radius:999px;overflow:hidden;background:#eef0f3}
.achievement-card-progress span{display:block;height:100%;border-radius:inherit;background:#6366f1}
.achievement-card.locked .achievement-card-progress span{background:#9ca3af}
.achievement-empty{display:none;padding:35px;text-align:center;color:#6b7280;background:#fff;border:1px dashed #d1d5db;border-radius:18px}
@media(max-width:1000px){.achievement-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:650px){.achievement-title{font-size:25px}.achievement-summary{width:100%}.achievement-grid{grid-template-columns:1fr}.achievement-card{min-height:auto}}
</style>

<div class="achievement-page">
    <div class="achievement-header">
        <div>
            <h1 class="achievement-title">Achievements</h1>
            <p class="achievement-subtitle">Keep learning and collect badges as you progress through KIDSS.</p>
        </div>

        <div class="achievement-summary">
            <div class="achievement-summary-top">
                <span>Achievements</span>
                <span class="achievement-summary-count"><?php echo $unlockedAchievements; ?>/<?php echo $totalAchievements; ?></span>
            </div>
            <div class="achievement-progress">
                <div class="achievement-progress-bar"></div>
            </div>
            <div style="margin-top:7px;font-size:12px;color:#6b7280"><?php echo $achievementPercentage; ?>% completed</div>
        </div>
    </div>

    <div class="achievement-filters">
        <button class="achievement-filter active" type="button" data-category="All">All</button>
        <button class="achievement-filter" type="button" data-category="Learning">Learning</button>
        <button class="achievement-filter" type="button" data-category="Quiz">Quiz</button>
        <button class="achievement-filter" type="button" data-category="Practice">Practice</button>
        <button class="achievement-filter" type="button" data-category="Vocabulary">Vocabulary</button>
    </div>

    <div class="achievement-grid" id="achievementGrid">
        <?php foreach ($achievementData as $achievement): ?>
            <?php
            $progress = max(0, min((int)$achievement['progress'], (int)$achievement['target']));
            $target = max(1, (int)$achievement['target']);
            $progressPercent = round(($progress / $target) * 100);
            ?>
            <article class="achievement-card <?php echo $achievement['unlocked'] ? '' : 'locked'; ?>" data-category="<?php echo htmlspecialchars($achievement['category']); ?>">
                <div class="achievement-status <?php echo $achievement['unlocked'] ? '' : 'locked'; ?>">
                    <?php echo $achievement['unlocked'] ? 'Unlocked' : 'Locked'; ?>
                </div>
                <div class="achievement-icon">
                    <i class="bi <?php echo htmlspecialchars($achievement['icon']); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($achievement['title']); ?></h3>
                <p><?php echo htmlspecialchars($achievement['description']); ?></p>
                <div class="achievement-meta">
                    <span>Progress</span>
                    <strong><?php echo $progress; ?>/<?php echo $target; ?></strong>
                </div>
                <div class="achievement-card-progress">
                    <span style="width:<?php echo $progressPercent; ?>%"></span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="achievement-empty" id="achievementEmpty">No achievements found in this category.</div>
</div>

<script>
(function(){
    const filters=document.querySelectorAll('.achievement-filter');
    const cards=document.querySelectorAll('.achievement-card');
    const empty=document.getElementById('achievementEmpty');

    filters.forEach(function(filter){
        filter.addEventListener('click',function(){
            const category=filter.dataset.category;
            let visible=0;

            filters.forEach(function(button){button.classList.remove('active')});
            filter.classList.add('active');

            cards.forEach(function(card){
                const show=category==='All'||card.dataset.category===category;
                card.style.display=show?'':'none';
                if(show) visible++;
            });

            empty.style.display=visible?'none':'block';
        });
    });
})();
</script>