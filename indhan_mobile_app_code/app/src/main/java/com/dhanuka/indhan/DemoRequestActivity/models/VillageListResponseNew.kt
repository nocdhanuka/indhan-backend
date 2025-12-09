package com.dhanuka.indhan.DemoRequestActivity.models

data class VillageListResponseNew(
    val `data`: List<VillageListNewData>,
    val message: String,
    val status: String,
    val status_code: Int
)