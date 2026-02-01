<?php
/**
 * @psalm-type Product = array{
 *  name: string,
 *  unit: string,
 *  quantity: int|float,
 * }
 * 
 * @psalm-type Config = array{
 *  title: string,
 *  products: Product[],
 *  searchUrl: string
 * }
 */