package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersDesignationData

@Dao
interface InfluencersDesignationDataListDao {
    @Insert
    fun insert(data: InfluencersDesignationData?): Long

    @get:Query("SELECT * FROM InfluencersDesignationData")
    val allDesignations: List<InfluencersDesignationData?>?

    @Query("DELETE FROM InfluencersDesignationData")
    fun deleteTableData()
}