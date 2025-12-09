package com.dhanuka.indhan.DemoRequestActivity.models

data class DistrictResponseNew(
    val `data`: List<DistrictDataNew>,
    val message: String,
    val status: String,
    val status_code: Int
)