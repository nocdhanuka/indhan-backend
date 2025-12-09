package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.ContactInfluencers

@Dao
interface ContactInfluencersDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: ContactInfluencers)

    @Query("SELECT * FROM ContactInfluencers")
    fun getAllContactListData(): List<ContactInfluencers?>?

    @Query("SELECT * FROM ContactInfluencers WHERE influencer_id =:id")
    fun getAllContactById(id: Int): List<ContactInfluencers?>?
}