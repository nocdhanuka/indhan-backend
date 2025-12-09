package com.dhanuka.indhan.DemoRequestActivity.models

import androidx.room.Entity
import androidx.room.PrimaryKey
import java.io.Serializable

@Entity
data class DemoRequestData(
    val acres: String,
    val contact_number: String,
    val created_at: String,
    val crop_id: String,
    val dd_id: String,
    val farmer_type: String,
    val demo_request_id: Int,
    val demo_code: String,
    val district: String,
    val district_id: String,
    val farmer_name: String,
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
    val village_id: String,
    val village: String,
    val demoImage: String,
    val farmer_image: String,
    val latitude: String,
    val longitude: String,
    var isSelected: Boolean,
    val crop: String
) : Serializable {

    @PrimaryKey(autoGenerate = true)
    var _id: Int? = null
}