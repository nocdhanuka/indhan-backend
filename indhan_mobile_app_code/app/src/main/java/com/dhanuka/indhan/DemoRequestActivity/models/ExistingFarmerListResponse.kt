package com.dhanuka.indhan.DemoRequestActivity.models

import java.io.Serializable

data class ExistingFarmerListResponse(
    val `data`: List<ExistingData>,
    val message: String,
    val status: String,
    val status_code: Int
) : Serializable