package com.dhanuka.indhan.institutional.model.influencers

import androidx.room.Entity
import androidx.room.PrimaryKey

data class InfluencersTypeDesignationFlModel(
    val crr_date: String,
    val `data`: Data,
    val message: String,
    val status: String,
    val status_code: Int
)

data class Data(
    val category_data: List<InfluencersCategoryData>,
    val company_data: List<InfluencersCompanyData>,
    val designation_data: List<InfluencersDesignationData>,
    val fl_data: List<InfluencersFlData>
)

@Entity
data class InfluencersCategoryData(
    val company_category: String,
    @PrimaryKey
    val influencer_category_id: Int
)

@Entity
data class InfluencersCompanyData(
    @PrimaryKey(autoGenerate = true)
    val _id: Int,
    val company_type: String,
    val influencer_company_id: Int
)

@Entity
data class InfluencersDesignationData(
    @PrimaryKey(autoGenerate = true)
    val _id: Int,
    val designation: String,
    val level: String,
    val influencer_designation_id: Int
)

@Entity
data class InfluencersFlData(
    @PrimaryKey
    val ECN: String,
    val manager_id: String,
    val id: Int,
    val manager_name: String,
    val name: String
)