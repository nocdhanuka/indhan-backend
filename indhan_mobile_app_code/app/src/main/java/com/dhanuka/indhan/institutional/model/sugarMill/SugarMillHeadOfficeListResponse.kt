package com.dhanuka.indhan.institutional.model.sugarMill

import androidx.room.Entity
import androidx.room.PrimaryKey

data class SugarMillHeadOfficeListResponse(
    val `data`: List<SugarMillHeadOfficeData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class SugarMillHeadOfficeData(
    @PrimaryKey(autoGenerate = true)
    val id: Int,
    val sugarmill_id: Int,
    val name: String?,
    val sugarmill_category_id: Int?,
    val user_ecn:String?,
    val office_type:String?,
    val type:String?
)