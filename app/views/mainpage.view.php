<?php $title = "Booking"; ?>
<?php require "../inc/header.php"; ?>

<main class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-1">Book your resort stay</h1>
            <p class="text-muted mb-0">
                Choose a resort, pick your dates, and confirm your booking.
            </p>
        </div>
        <div class="col-auto text-end">
            <span class="fw-semibold">
                <?= isset($_SESSION['user']['username']) ? 'Welcome, ' . htmlspecialchars($_SESSION['user']['username']) : '' ?>
            </span>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <strong>Available Resorts</strong>
                </div>
                <div class="card-body">
                    <?php if (!empty($resorts)): ?>
                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            <?php foreach ($resorts as $resort): ?>
                                <div class="col">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-1">
                                                <?= htmlspecialchars($resort['name']) ?>
                                            </h5>
                                            <?php if (!empty($resort['location'])): ?>
                                                <div class="text-muted small mb-2">
                                                    <?= htmlspecialchars($resort['location']) ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($resort['description'])): ?>
                                                <p class="card-text small mb-2">
                                                    <?= htmlspecialchars($resort['description']) ?>
                                                </p>
                                            <?php endif; ?>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <?php if (isset($resort['price_per_night'])): ?>
                                                    <span class="fw-bold text-primary">
                                                        ₱<?= number_format($resort['price_per_night'], 2) ?>/night
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (isset($resort['max_guests'])): ?>
                                                    <span class="badge text-bg-light">
                                                        Up to <?= (int)$resort['max_guests'] ?> guests
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No resorts configured yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <strong>New Booking</strong>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="resort_id" class="form-label">Resort</label>
                            <select name="resort_id" id="resort_id" class="form-select" required>
                                <option value="">Select a resort</option>
                                <?php if (!empty($resorts)): ?>
                                    <?php foreach ($resorts as $resort): ?>
                                        <option value="<?= (int)$resort['id'] ?>">
                                            <?= htmlspecialchars($resort['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="check_in" class="form-label">Check-in</label>
                                <input type="date" name="check_in" id="check_in" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="check_out" class="form-label">Check-out</label>
                                <input type="date" name="check_out" id="check_out" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="guests" class="form-label">Guests</label>
                            <input type="number" min="1" name="guests" id="guests" class="form-control" value="1" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            Confirm Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Your Bookings</strong>
                </div>
                <div class="card-body">
                    <?php if (!empty($bookings)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Resort</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Guests</th>
                                        <th>Booked at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($booking['resort_name']) ?></td>
                                            <td><?= htmlspecialchars($booking['check_in']) ?></td>
                                            <td><?= htmlspecialchars($booking['check_out']) ?></td>
                                            <td><?= (int)$booking['guests'] ?></td>
                                            <td><?= htmlspecialchars($booking['created_at']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">You have no bookings yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require "../inc/footer.php"; ?>
