<?php
session_start();
require 'config.php';

if (!isset($_GET['perfume_id']) || !is_numeric($_GET['perfume_id'])) {
    header("Location: index.php");
    exit;
}

$perfumeId = intval($_GET['perfume_id']);
$userId = $_SESSION['user_id'] ?? null;

// Fetch perfume details
$sql = "
    SELECT p.id, p.perfume_name, p.description, p.accords, p.notes, 
           p.fashion_styles, p.image, p.launch_year, 
           b.name AS brand_name, 
           pf.id AS perfumer_id, pf.name AS perfumer_name,
           GROUP_CONCAT(ff.name SEPARATOR ', ') AS fragrance_families,
           (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.perfume_id = p.id) AS avg_rating
    FROM perfumes p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN perfumers pf ON p.perfumer_id = pf.id
    LEFT JOIN perfume_families pfm ON p.id = pfm.perfume_id
    LEFT JOIN fragrance_families ff ON pfm.family_id = ff.id
    WHERE p.id = ?
    GROUP BY p.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $perfumeId);
$stmt->execute();
$result = $stmt->get_result();
$perfume = $result->fetch_assoc();

if (!$perfume) {
    header("Location: index.php");
    exit;
}

// Pagination
$reviewsPerPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $reviewsPerPage;

// Fetch perfume reviews with pagination
$reviewSql = "
    SELECT u.username, r.rating, r.review_text, r.scent_impression, r.created_at, r.id AS review_id
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.perfume_id = ?
    ORDER BY r.created_at DESC
    LIMIT ?, ?";
$reviewStmt = $conn->prepare($reviewSql);
$reviewStmt->bind_param("iii", $perfumeId, $offset, $reviewsPerPage);
$reviewStmt->execute();
$reviews = $reviewStmt->get_result();

// Total review count for pagination
$countSql = "SELECT COUNT(*) AS total FROM reviews WHERE perfume_id = ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("i", $perfumeId);
$countStmt->execute();
$totalReviews = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalReviews / $reviewsPerPage);

// Check if user already reviewed this perfume
$userReviewSql = "SELECT * FROM reviews WHERE user_id = ? AND perfume_id = ?";
$userReviewStmt = $conn->prepare($userReviewSql);
$userReviewStmt->bind_param("ii", $userId, $perfumeId);
$userReviewStmt->execute();
$userReview = $userReviewStmt->get_result()->fetch_assoc();
?>

<div class="container mt-4">
    <div class="row">
        <!-- Left Side: Perfume Details -->
        <div class="col-md-7">
            <h1><?= htmlspecialchars($perfume['perfume_name']) ?></h1>
            <p class="text-muted"><?= htmlspecialchars($perfume['brand_name']) ?> (<?= htmlspecialchars($perfume['launch_year'] ?? 'N/A') ?>)</p>

            <p><strong>Description:</strong> <?= htmlspecialchars($perfume['description'] ?? 'N/A') ?></p>
            <p><strong>Accords:</strong> <?= htmlspecialchars($perfume['accords'] ?? 'N/A') ?></p>
            <p><strong>Notes:</strong> <?= htmlspecialchars($perfume['notes'] ?? 'N/A') ?></p>

            <p><strong>Perfumer:</strong> 
                <?php if (!empty($perfume['perfumer_name'])): ?>
                    <a href="perfumer_details.php?perfumer_id=<?= urlencode($perfume['perfumer_id']) ?>">
                        <?= htmlspecialchars($perfume['perfumer_name']) ?>
                    </a>
                <?php else: ?>
                    <?= htmlspecialchars('Unknown') ?>
                <?php endif; ?>
            </p>

            <p><strong>Fashion Styles:</strong> <?= htmlspecialchars($perfume['fashion_styles'] ?? 'N/A') ?></p>
            <p><strong>Fragrance Families:</strong> <?= htmlspecialchars($perfume['fragrance_families'] ?? 'N/A') ?></p>
        </div>

        <!-- Right Side: Image -->
        <div class="col-md-5 text-center">
            <img src="<?= !empty($perfume['image']) ? 'uploads/perfume/' . htmlspecialchars($perfume['image']) : 'assets/perfume-placeholder.png' ?>" 
                 alt="<?= htmlspecialchars($perfume['perfume_name']) ?>" class="img-fluid rounded shadow">
        </div>
    </div>

    <!-- Summary Rating -->
    <div class="mt-3 text-center">
        <h3>Overall Rating: 
            <?= $perfume['avg_rating'] ? number_format($perfume['avg_rating'], 1) : "No Ratings Yet" ?> 
            ⭐
        </h3>
    </div>

    <!-- Ratings & Reviews Section -->
    <div class="mt-5">
        <h3 class="text-center">Ratings & Reviews</h3>

        <?php if ($userId): ?>
            <div class="text-center">
                <?php if (!$userReview): ?>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#reviewModal">Add Review</button>
                <?php else: ?>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editReviewModal">Edit Your Review</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<!-- Edit Review Modal -->
<div class="modal fade" id="editReviewModal" tabindex="-1" aria-labelledby="editReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Your Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="edit_review.php" method="POST">
                    <input type="hidden" name="review_id" value="<?= $userReview['id'] ?>">
                    <input type="hidden" name="perfume_id" value="<?= $perfumeId ?>">

                    <div class="mb-3">
                        <label class="form-label">Rating:</label>
                        <select class="form-select" name="rating" required>
                            <option value="5" <?= $userReview['rating'] == 5 ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ - Excellent</option>
                            <option value="4" <?= $userReview['rating'] == 4 ? 'selected' : '' ?>>⭐⭐⭐⭐ - Very Good</option>
                            <option value="3" <?= $userReview['rating'] == 3 ? 'selected' : '' ?>>⭐⭐⭐ - Good</option>
                            <option value="2" <?= $userReview['rating'] == 2 ? 'selected' : '' ?>>⭐⭐ - Fair</option>
                            <option value="1" <?= $userReview['rating'] == 1 ? 'selected' : '' ?>>⭐ - Poor</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Review:</label>
                        <textarea class="form-control" name="review" rows="3" required><?= htmlspecialchars($userReview['review_text']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Scent Impression:</label>
                        <input type="text" class="form-control" name="scent_impression" value="<?= htmlspecialchars($userReview['scent_impression']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Display Submitted Reviews -->
    <div class="mt-4">
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($review['username']) ?></h5>
                    <p class="text-warning">
                        <?= str_repeat("⭐", $review['rating']) ?> (<?= $review['rating'] ?>/5)
                    </p>
                    <p class="card-text"><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                    <p><strong>Scent Impression:</strong> <?= htmlspecialchars($review['scent_impression']) ?></p>
                    <p class="text-muted"><?= date("F j, Y", strtotime($review['created_at'])) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<!-- Add Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="submit_review.php" method="POST">
                    <input type="hidden" name="perfume_id" value="<?= $perfumeId ?>">

                    <div class="mb-3">
                        <label class="form-label">Rating:</label>
                        <select class="form-select" name="rating" required>
                            <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                            <option value="4">⭐⭐⭐⭐ - Very Good</option>
                            <option value="3">⭐⭐⭐ - Good</option>
                            <option value="2">⭐⭐ - Fair</option>
                            <option value="1">⭐ - Poor</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Review:</label>
                        <textarea class="form-control" name="review" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Scent Impression:</label>
                        <input type="text" class="form-control" name="scent_impression" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</div>
    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?perfume_id=<?= $perfumeId ?>&page=<?= $page - 1 ?>">Prev</a></li>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?perfume_id=<?= $perfumeId ?>&page=<?= $page + 1 ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Include footer -->
<?php include 'footer.php'; ?>
