<div class="container">
    <div class="view_panel">
        <h1 class="view_header">
            Support manager
        </h1>
        <div class="view_content">
            <!-- Search -->
            <div class="search-bar">
                <input 
                    type="text" 
                    class="search-bar-big" 
                    placeholder="Search bundle" 
                />
                <button 
                    class="search-bar-btn" 
                    onClick="search(this)"
                >
                    &#128270;
                </button>
            </div>
            
            <div class="view_widget">
                <!-- Filters -->
                <div class="search-filters">
                    <h3>
                        Filter by category
                    </h3>
                    <div class="search-filter-options">
                        <div class="form-group">
                            <select 
                                id="sel-category" 
                                name="sel-category" 
                                class="form-control"
                            >
                                <option value="0">
                                    All
                                </option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category->getId(); ?>">
                                        <?php echo ucfirst(strtolower($category->getName())); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Topics -->
            <table class="table table-hover table-bordered table-stripped">
                <thead>
                    <tr class="support_header">
                        <th>
                            Title
                        </th>
                        <th>
                            Category
                        </th>
                        <th class="support_date">
                            Date of publication
                        </th>
                        <th>
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody id="topics">
                    <?php foreach ($topics as $topic): ?>
                    <tr>
                        <td>
                            <a href="<?php echo BASE_URL."support/open/".$topic->getId(); ?>">
                                <?php echo $topic->getTitle(); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo ucfirst(strtolower($topic->getCategory()->getName())); ?>
                        </td>
                        <td>
                            <?php echo $topic->getCreationDate()->format("m/d/Y H:m:s"); ?>
                        </td>
                        <td>
                            <?php echo $topic->isClosed() ? "Closed" : "Open" ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <ul class="pagination pagination-sm justify-content-center">
                <li class="page-item <?php echo ($currentIndex - 1 <= 0) ? "disabled" : ""; ?>">
                    <a 
                        href="<?php echo BASE_URL."support?index=".($currentIndex - 1); ?>" 
                        class="page-link"
                    >
                        Before
                    </a>
                </li>
                <?php for ($i=1; $i<=$totalPages; $i++): ?>
                    <li 
                        class="page-item <?php echo $i == $currentIndex ? "active" : "" ?>" 
                        data-index="<?php echo $i; ?>"
                    >
                        <a 
                            href="<?php echo BASE_URL."support?index=".$i; ?>" 
                            class="page-link"
                        >
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($currentIndex + 1 > $totalPages) ? "disabled" : ""; ?>">
                    <a 
                        href="<?php echo BASE_URL."support?index=".($currentIndex + 1); ?>" 
                        class="page-link"
                    >
                        After
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>