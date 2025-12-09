package com.dhanuka.indhan.institutional.model.teaEstate

data class TeaEstateVisitSummaryResponse(
    val crr_date: String,
    val `data`: VisitSummaryData,
    val message: String,
    val status: String,
    val status_code: Int
)

data class VisitSummaryData(
    val cat_a_plan: Int,
    val cat_a_target: Int,
    val cat_a_visit: Int,
    val cat_b_plan: Int,
    val cat_b_target: Int,
    val cat_b_visit: Int,
    val cat_c_plan: Int,
    val cat_c_target: Int,
    val cat_c_visit: Int,
    val month: Int,
    val year: Int
)