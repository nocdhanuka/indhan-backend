package com.dhanuka.indhan.DemoRequestEvaluated.models

data class UtilizationSummary(
    val `data`: ArrayList<UtilizationSummaryData>,
    val message: String,
    val status: String,
    val status_code: Int
)

data class UtilizationSummaryData(
    val crop_id: Int,
    val crop_name: String,
    val demo_applied: Int,
    val demo_done: Int,
    val product_id: Int,
    val product_name: String,
    val received_materials: String,
    val s1_ecn_name: String,
    val s1_ecn_no: String,
    val s1_ecn_type: String,
    val waste: Int
)