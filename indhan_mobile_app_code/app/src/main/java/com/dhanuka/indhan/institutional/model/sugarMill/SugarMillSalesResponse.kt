package com.dhanuka.indhan.institutional.model.sugarMill

import androidx.room.Entity
import androidx.room.PrimaryKey

data class SugarMillSalesResponse(
    val `data`: List<SugarMillSalesData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class SugarMillSalesData(
    val month: String?,
    @PrimaryKey
    val sugarmill_id: Int?,
    val sugarmill_name: String?,
    val sales_figure: String?,
    val year: String?
)