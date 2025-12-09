package com.dhanuka.indhan.institutional.model

data class AddDistributorResponse(
    val `data`: List<DistributorData>,
    val message: String,
    val status: String,
    val status_code: Int
)

data class DistributorData(
    val address: String,
    val brands: String,
    val created_at: String,
    val crope: String,
    val distributor_advisor_id: Int,
    val distributor_firm_name: String,
    val distributor_name: String,
    val distributor_type: String,
    val district_id: Int,
    val is_distributor: Int,
    val is_visited: Int,
    val market_id: Int,
    val mobile: String,
    val mobile2: String,
    val pincode: String,
    val state_id: Int,
    val status: Int,
    val territory_id: Int,
    val turnover: String,
    val updated_at: String,
    val user_ecn: String,
    val user_id: Int,
    val user_name: String,
    val user_type: String
)