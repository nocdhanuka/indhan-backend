package com.dhanuka.indhan.DemoRequestActivity.models

import com.dhanuka.indhan.DemoRequestActivity.model.ProductlistNew
import java.io.Serializable

data class ExistingData(
    val acpVisit: Int,
    val acres: String,
    val alternateContactNumber: String,
    val call_status: String,
    val contactNumber: String,
    val district: String,
    val districtId: String,
    val farmarImage: String,
    val farmerId: String,
    val farmerName: String,
    val fatherName: String,
    val lead_status: String,
    val crops: List<Crop>,
    val products: List<ProductlistNew>,
    val state: String,
    val stateId: String,
    val village: String,
    val village_id: String,
    val farmerLat: String,
    val farmerLong: String,
    val tehsilId: String,
    val tehsil: String,
    val crop_id: String,
    val crop: String,
    val product_id: String,
    val product: String
) : Serializable