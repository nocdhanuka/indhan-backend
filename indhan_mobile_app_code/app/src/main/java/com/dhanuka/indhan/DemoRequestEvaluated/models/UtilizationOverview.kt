package com.dhanuka.indhan.DemoRequestEvaluated.models

data class UtilizationOverview(
    val `data`: OverViewData,
    val message: String,
    val status: String,
    val status_code: Int
)

data class OverViewData(
    val totalRequest: Int,
    val total_admin_approval: Int,
    val total_dispatch: Int,
    val total_received: Int,
    val total_rsm_approval: Int
)