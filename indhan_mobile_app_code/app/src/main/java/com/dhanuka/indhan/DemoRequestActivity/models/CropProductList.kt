package com.dhanuka.indhan.DemoRequestActivity.models

data class CropProductList(
    val `data`: List<CropProductData>,
    val message: String,
    val status: String,
    val status_code: Int
)