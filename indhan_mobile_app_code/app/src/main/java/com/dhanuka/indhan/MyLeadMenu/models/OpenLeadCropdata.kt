package com.dhanuka.indhan.MyLeadMenu.models

import java.io.Serializable

data class OpenLeadCropdata(
    val acres: String,
    val created_at: String,
    val crop: String,
    val crop_id: String,
    val farmer_id: String,
    val product_id: String,
    val updated_at: String,
    val village_farmer_crop_id: String,
    val village_id: String
) : Serializable