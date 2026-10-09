<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$studentDisplayName = htmlspecialchars((string)($_SESSION['name'] ?? 'Student'), ENT_QUOTES, 'UTF-8');
?>
<div class="lessons-page-body">
    <!-- Header / Welcome & User Bar -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-12 col-md-6">
            <div class="lesson-heading">
                <div class="lesson-title-art">
                    <span class="lesson-title-letter">A</span>
                    <span class="title-sparkle ts-one">✦</span>
                    <span class="title-sparkle ts-two">✦</span>
                </div>
                <div>
                    <h1>Alphabet</h1>
                    <p>Learn A–Z signs in a fun and easy way!</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center gap-3">
            <!-- <div class="header-icon-pill">
                <i class="bi bi-bell-fill text-warning fs-5"></i>
                <span class="badge-count">3</span>
            </div>
            <div class="header-icon-pill">
                <i class="bi bi-star-fill text-warning fs-5"></i>
                <span class="badge-count" style="background: #f59e0b;">12</span>
            </div> -->
            <div class="profile-pill d-flex align-items-center">
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed=<?= rawurlencode((string)($_SESSION['name'] ?? 'Student')) ?>" alt="Student avatar" class="user-avatar">
                <div class="lh-1 me-2">
                    <div class="fw-bold fs-6"><?= $studentDisplayName ?></div>
                    <small class="text-muted" style="font-size: 0.75rem;"></small>
                </div>
                <i></i>
            </div>
        </div>
    </div>

    <!-- Start of Lessons Page Content -->
    <div class="lesson-page">

        <!-- ALPHABET CATEGORY -->
        <section class="lesson-category collapsible-category">
            <button
                class="lesson-category-bar category-toggle"
                type="button"
                data-target="alphabetLessons"
                aria-expanded="true"
                aria-controls="alphabetLessons">

                <div class="lesson-category-info">
                    <div class="lesson-book-icon">
                        <i class="bi bi-alphabet-uppercase"></i>
                    </div>
                    <div>
                        <h3>Alphabet</h3>
                        <span>26 Lessons</span>
                    </div>
                </div>

                <div class="lesson-progress-wrap">
                    <div class="lesson-progress-label">
                        <strong>6 / 26</strong> Completed
                    </div>
                    <div class="lesson-progress">
                        <div class="lesson-progress-fill" style="width: 23%;"></div>
                    </div>
                </div>

                <span class="category-chevron" aria-hidden="true">
                    <i class="bi bi-chevron-up"></i>
                </span>
            </button>

            <div id="alphabetLessons" class="category-content is-open">
                <div class="lesson-grid" id="alphabetGrid">
                    <!-- Letter A -->
                    <article class="lesson-card completed">
                        <div class="lesson-card-art art-red">
                            <span class="lesson-letter">A</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play" aria-label="Play Letter A"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row">
                                    <h2>Letter A</h2>
                                    <i class="bi bi-star-fill lesson-star earned"></i>
                                </div>
                                <p>Learn the sign for A</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:15</span>
                                <div class="lesson-status completed-status"><i class="bi bi-check-circle-fill"></i> Completed</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter A"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter B -->
                    <article class="lesson-card completed">
                        <div class="lesson-card-art art-blue">
                            <span class="lesson-letter">B</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play" aria-label="Play Letter B"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row">
                                    <h2>Letter B</h2>
                                    <i class="bi bi-star-fill lesson-star earned"></i>
                                </div>
                                <p>Learn the sign for B</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:10</span>
                                <div class="lesson-status completed-status"><i class="bi bi-check-circle-fill"></i> Completed</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter B"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter C -->
                    <article class="lesson-card in-progress">
                        <div class="lesson-card-art art-yellow">
                            <span class="lesson-letter">C</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play" aria-label="Play Letter C"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row">
                                    <h2>Letter C</h2>
                                    <i class="bi bi-star-fill lesson-star earned"></i>
                                </div>
                                <p>Learn the sign for C</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:08</span>
                                <div class="lesson-status progress-status"><i class="bi bi-lock-fill"></i> In Progress</div>
                            </div>
                            <div class="lesson-percent">60%</div>
                            <button class="lesson-next" aria-label="Open Letter C"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter D -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-green">
                            <span class="lesson-letter">D</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter D locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row">
                                    <h2>Letter D</h2>
                                    <i class="bi bi-star-fill lesson-star"></i>
                                </div>
                                <p>Learn the sign for D</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:12</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter D"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter E -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-red">
                            <span class="lesson-letter">E</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter E locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter E</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for E</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:05</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter E"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter F -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-blue">
                            <span class="lesson-letter">F</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter F locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter F</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for F</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:18</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter F"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter G -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-yellow">
                            <span class="lesson-letter">G</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter G locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter G</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for G</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:14</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter G"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter H -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-green">
                            <span class="lesson-letter">H</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter H locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter H</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for H</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:11</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter H"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter I -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-red">
                            <span class="lesson-letter">I</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter I locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter I</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for I</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:09</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter I"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter J -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-blue">
                            <span class="lesson-letter">J</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter J locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter J</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for J</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:16</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter J"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter K -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-yellow">
                            <span class="lesson-letter">K</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter K locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter K</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for K</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:13</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter K"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter L -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-green">
                            <span class="lesson-letter">L</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter L locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter L</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for L</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:10</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter L"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter M -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-red">
                            <span class="lesson-letter">M</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter M locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter M</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for M</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:15</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter M"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter N -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-blue">
                            <span class="lesson-letter">N</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter N locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter N</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for N</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:07</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter N"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter O -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-yellow">
                            <span class="lesson-letter">O</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter O locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter O</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for O</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:12</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter O"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter P -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-green">
                            <span class="lesson-letter">P</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter P locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter P</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for P</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:14</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter P"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter Q -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-red">
                            <span class="lesson-letter">Q</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter Q locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter Q</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for Q</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:19</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter Q"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter R -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-blue">
                            <span class="lesson-letter">R</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter R locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter R</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for R</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:11</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter R"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter S -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-yellow">
                            <span class="lesson-letter">S</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter S locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter S</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for S</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:08</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter S"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter T -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-green">
                            <span class="lesson-letter">T</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter T locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter T</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for T</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:13</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter T"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter U -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-red">
                            <span class="lesson-letter">U</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter U locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter U</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for U</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:15</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter U"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter V -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-blue">
                            <span class="lesson-letter">V</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter V locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter V</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for V</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:10</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter V"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter W -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-yellow">
                            <span class="lesson-letter">W</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter W locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter W</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for W</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:17</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter W"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter X -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-green">
                            <span class="lesson-letter">X</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter X locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter X</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for X</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:12</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter X"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter Y -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-red">
                            <span class="lesson-letter">Y</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter Y locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter Y</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for Y</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:14</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter Y"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>

                    <!-- Letter Z -->
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-blue">
                            <span class="lesson-letter">Z</span>
                            <span class="lesson-child girl">👧🏻</span>
                            <button class="lesson-play locked-play" aria-label="Letter Z locked"><i class="bi bi-play-fill"></i></button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row"><h2>Letter Z</h2><i class="bi bi-star-fill lesson-star"></i></div>
                                <p>Learn the sign for Z</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:20</span>
                                <div class="lesson-status locked-status"><i class="bi bi-lock-fill"></i> Not Started</div>
                            </div>
                            <button class="lesson-next" aria-label="Open Letter Z"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>
                </div>

                <nav class="lesson-pagination" id="alphabetPagination" aria-label="Alphabet lesson pages">
                    <!-- Dynamic page numbers generated by JS -->
                </nav>
            </div>
        </section>

        <!-- GREETINGS CATEGORY -->
        <section class="lesson-category collapsible-category">
            <button
                class="lesson-category-bar category-toggle"
                type="button"
                data-target="greetingsLessons"
                aria-expanded="false"
                aria-controls="greetingsLessons">

                <div class="lesson-category-info">
                    <div class="lesson-book-icon">
                        <i class="bi bi-chat-heart-fill"></i>
                    </div>
                    <div>
                        <h3>Greetings</h3>
                        <span>4 Lessons</span>
                    </div>
                </div>

                <div class="lesson-progress-wrap">
                    <div class="lesson-progress-label">
                        <strong>0 / 4</strong> Completed
                    </div>
                    <div class="lesson-progress">
                        <div class="lesson-progress-fill" style="width: 0%;"></div>
                    </div>
                </div>

                <span class="category-chevron" aria-hidden="true">
                    <i class="bi bi-chevron-down"></i>
                </span>
            </button>

            <div id="greetingsLessons" class="category-content" hidden>
                <div class="lesson-grid">
                    <article class="lesson-card not-started">
                        <div class="lesson-card-art art-red">
                            <span class="lesson-letter">👋</span>
                            <span class="lesson-child boy">👦🏻</span>
                            <button class="lesson-play locked-play" aria-label="Play Hello">
                                <i class="bi bi-play-fill"></i>
                            </button>
                        </div>
                        <div class="lesson-card-body">
                            <div class="lesson-main-copy">
                                <div class="lesson-name-row">
                                    <h2>Hello</h2>
                                    <i class="bi bi-star-fill lesson-star"></i>
                                </div>
                                <p>Learn the sign for Hello</p>
                                <span class="lesson-duration"><i class="bi bi-clock"></i> 02:10</span>
                                <div class="lesson-status locked-status">
                                    <i class="bi bi-lock-fill"></i> Not Started
                                </div>
                            </div>
                            <button class="lesson-next" aria-label="Open Hello"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="js/lesson_script.js"></script>