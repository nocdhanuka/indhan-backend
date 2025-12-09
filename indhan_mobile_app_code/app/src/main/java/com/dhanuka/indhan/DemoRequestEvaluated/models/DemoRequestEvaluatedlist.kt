package com.dhanuka.indhan.DemoRequestEvaluated.models

import java.io.Serializable

data class DemoRequestEvaluatedlist(
    val acres: Any,
    val contact_number: String,
    val created_at: String,
    val crop: String,
    val crop_id: String,
    val dd_id: String,
    val dd_name: String,
    val demoImage: String,
    val demo_code: String,
    val demo_feedback: List<DemoRequestEvaluateFeedback>,
    val demo_request_id: Int,
    val district: String,
    val district_id: String,
    val farmer_image: String,
    val farmer_name: String,
    val farmer_type: String,
    val latitude: String,
    val longitude: String,
    val pincode: String,
    val product: String,
    val product_id: String,
    val showing_date: String,
    val spray: String,
    val state: String,
    val state_id: String,
    val status: String,
    val tehsil: String,
    val tehsil_id: String,
    val updated_at: String,
    val village: String,
    val village_id: String
) : Serializable