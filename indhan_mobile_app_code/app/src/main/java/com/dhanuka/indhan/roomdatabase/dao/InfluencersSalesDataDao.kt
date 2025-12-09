package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersSalesData

@Dao
interface InfluencersSalesDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: InfluencersSalesData)

    @Query("SELECT * FROM InfluencersSalesData")
    fun getAllSalesListData(): List<InfluencersSalesData?>?

    @Query("SELECT * FROM InfluencersSalesData WHERE influencer_id =:id")
    fun getAllSalesById(id: Int): List<InfluencersSalesData?>?

    @Query("SELECT * FROM InfluencersSalesData WHERE influencer_id =:id AND month=:month AND year=:year")
    fun getAllSalesById(id: Int, month: String, year: String): List<InfluencersSalesData?>?

    @Query("SELECT * FROM InfluencersSalesData WHERE month=:month AND year=:year")
    fun getAllSalesById(month: String, year: String): List<InfluencersSalesData?>?

    @Query("DELETE FROM InfluencersSalesData WHERE influencer_id =:id")
    fun deleteTableData(id: Int)
}