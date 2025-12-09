package com.dhanuka.indhan.institutional.model.influencers

import androidx.room.Entity
import androidx.room.PrimaryKey

data class InfluencersHeadOfficeListResponse(
    val `data`: List<InfluencersHeadOfficeData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class InfluencersHeadOfficeData(
    @PrimaryKey(autoGenerate = true)
    val id: Int,
    val influencer_id: Int,
    val name: String?,
    val influencer_category_id: Int?,
    val user_ecn: String?,
    val office_type: String?,
    val type: String?
)