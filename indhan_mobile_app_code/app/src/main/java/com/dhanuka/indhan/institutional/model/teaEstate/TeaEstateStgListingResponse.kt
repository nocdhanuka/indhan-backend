package com.dhanuka.indhan.institutional.model.teaEstate

import androidx.room.Entity
import androidx.room.PrimaryKey

data class TeaEstateStgListingResponse(
    val `data`: List<TeaEstateStgData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class TeaEstateStgData(
    @PrimaryKey(autoGenerate = true)
    val id: Int,
    val tea_estate_id: Int,
    val name: String?,
    val estate_category_id: Int?,
    val user_ecn:String?,
    val office_type:String?,
    val type:String?
)