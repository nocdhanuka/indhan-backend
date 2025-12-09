package com.dhanuka.indhan.institutional.model

import androidx.room.Entity
import androidx.room.PrimaryKey

data class TeaEstateSalesResponse(
    val `data`: List<TeaEstateSalesData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class TeaEstateSalesData(
    @PrimaryKey
    val id: Int,
    val month: String?,
    val tea_estate_id: Int?,
    val tea_estate_name: String?,
    val sales_figure: String?,
    val year: String?
)