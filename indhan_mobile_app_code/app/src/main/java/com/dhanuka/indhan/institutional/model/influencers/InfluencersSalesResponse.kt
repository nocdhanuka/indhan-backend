package com.dhanuka.indhan.institutional.model.influencers

import androidx.room.Entity
import androidx.room.PrimaryKey

data class InfluencersSalesResponse(
    val `data`: List<InfluencersSalesData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class InfluencersSalesData(
    val month: String?,
    @PrimaryKey
    val influencer_id: Int?,
    val influencer_name: String?,
    val sales_figure: String?,
    val year: String?
)