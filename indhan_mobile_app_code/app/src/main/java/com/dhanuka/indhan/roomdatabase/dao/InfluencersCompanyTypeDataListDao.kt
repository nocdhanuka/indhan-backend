package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersCompanyData

@Dao
interface InfluencersCompanyTypeDataListDao {
    @Insert
    fun insert(data: InfluencersCompanyData?): Long

    @get:Query("SELECT * FROM InfluencersCompanyData")
    val allCompanyType: List<InfluencersCompanyData?>?

    @Query("DELETE FROM InfluencersCompanyData")
    fun deleteTableData()
}