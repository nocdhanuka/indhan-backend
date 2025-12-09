package com.dhanuka.indhan.Activity.misAndReports.model

import androidx.room.Entity
import androidx.room.PrimaryKey

data class MisReportsListResponse(
    val base_url: String,
    val crr_date: String,
    val `data`: List<ReportsListData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class ReportsListData(
    @PrimaryKey
    val report_id: Int,
    val created_at: String,
    val month: Int,
    val report_name: String,
    val report_type_id: Int,
    val status: Int,
    val updated_at: String,
    val uploaded_file: String,
    val year: Int
)