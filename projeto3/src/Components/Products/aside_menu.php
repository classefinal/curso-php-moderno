<?php

/**
 * @var int $limit
 */
?>

<aside class="col-12 col-sm-12 col-md-4 col-lg-3 mt-4">
    <p>Opções</p>
    <div class="accordion" id="filterOptions">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Quantidade de itens
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#filterOptions">
                <div class="accordion-body">
                    <menu class="list-unstyled">
                        <li>
                            <a href="/produtos?limit=10" class="text-decoration-none">
                                <?php if (!isset($limit) || $limit === 10): ?>
                                    <strong>10 itens por página</strong>
                                <?php else: ?>
                                    10 itens por página
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="/produtos?limit=20" class="text-decoration-none">
                                <?php if (isset($limit) && $limit === 20): ?>
                                    <strong>20 itens por página</strong>
                                <?php else: ?>
                                    20 itens por página
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="/produtos?limit=30" class="text-decoration-none">
                                <?php if (isset($limit) && $limit === 30): ?>
                                    <strong>30 itens por página</strong>
                                <?php else: ?>
                                    30 itens por página
                                <?php endif; ?>
                            </a>
                        </li>
                    </menu>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Accordion Item #2
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#filterOptions">
                <div class="accordion-body">
                    <strong>This is the second item’s accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It’s also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Accordion Item #3
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#filterOptions">
                <div class="accordion-body">
                    <strong>This is the third item’s accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It’s also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                </div>
            </div>
        </div>
    </div>
</aside>