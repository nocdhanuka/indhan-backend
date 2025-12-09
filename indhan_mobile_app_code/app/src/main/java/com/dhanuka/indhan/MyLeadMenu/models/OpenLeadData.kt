package com.dhanuka.indhan.MyLeadMenu.models

import java.io.Serializable

data class OpenLeadData(
    val created_at: String,
    val cropdata: List<OpenLeadCropdata>,
    val expected_purchase_date: String,
    val farmer_id: String,
    val farmer_type: String,
    val lead_id: String,
    val lead_code: String,
    val lead_status: String,
    val product: String,
    val product_id: String,
    val purchased_area: String,
    val purchased_from: String,
    val updated_at: String,
    val user_id: String,
    val user_type: String,
    val leadName: String,
    val villageName: String,
    val contact_number: String,
    val distributor_name: String,
    val distributor_id: String,
    val call_status: String
) : Serializable