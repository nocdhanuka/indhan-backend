package com.dhanuka.indhan.Activity.misAndReports.model

import androidx.room.Entity
import androidx.room.PrimaryKey

data class MisSubMenuResponse(
    val base_url: String,
    val crr_date: String,
    val `data`: List<MisSubMenu>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class MisSubMenu(
    val created_at: String,
    val image_url: String,
    val name: String,
    @PrimaryKey
    val report_type_id: Int,
    val status: Int,
    val updated_at: String
)