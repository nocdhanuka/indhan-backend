package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersFlData

@Dao
interface InfluencersFlDataListDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: InfluencersFlData?): Long

    @get:Query("SELECT * FROM InfluencersFlData")
    val allFlListType: List<InfluencersFlData?>?

    @Query("SELECT * FROM InfluencersFlData WHERE manager_id =:managerId")
    fun getAllFlByManagerEcn(managerId: String?): List<InfluencersFlData?>?

    @Query("DELETE FROM InfluencersFlData")
    fun deleteTableData()
}