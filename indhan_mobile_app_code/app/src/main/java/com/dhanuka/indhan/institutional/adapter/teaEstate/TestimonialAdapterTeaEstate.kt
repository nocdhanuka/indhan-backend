package com.dhanuka.indhan.institutional.adapter.teaEstate

import android.content.Context
import android.graphics.Bitmap
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.bumptech.glide.load.DataSource
import com.bumptech.glide.load.engine.GlideException
import com.bumptech.glide.request.RequestListener
import com.bumptech.glide.request.RequestOptions
import com.bumptech.glide.request.target.Target
import com.dhanuka.indhan.Constants.Constants.IMAGEBASEURL
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.TestimonialListData
import kotlinx.android.synthetic.main.item_tea_estate_testimonial.view.*

class TestimonialAdapterTeaEstate(
    var context: Context,
    var listOfData: ArrayList<TestimonialListData>,
    var fromCircular: Boolean
) : RecyclerView.Adapter<TestimonialAdapterTeaEstate.ViewHolder>() {
    private lateinit var clickListener: OnClickItem

    fun setClickListener(itemClickListener: OnClickItem) {
        this.clickListener = itemClickListener
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.item_tea_estate_testimonial, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.itemView.txtTestimonialId.text = "T".plus(listOfData[position].institutional_testimonial_id)
        holder.itemView.txtName.text = listOfData[position].institutional_name
            .plus("\n").plus(listOfData[position].testimonial_name).plus(" | ")
            .plus(getInstitutionType(listOfData[position].institutionalType))
        holder.itemView.txtDescription.text =
            "Description :- ".plus(listOfData[position].description ?: "")
        holder.itemView.ratingBar.text =
            "Can be shared :- ".plus(if (listOfData[position].can_be_shared == "1") "YES" else "NO")
        holder.itemView.txtCreatedAt.text = listOfData[position].date_time ?: ""
        holder.itemView.language.text =
            "Language :- ".plus(listOfData[position].language_name ?: "")

        val requestOptions = RequestOptions()
        requestOptions.placeholder(R.drawable.video_placeholder)
        requestOptions.centerInside()

        holder.itemView.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_INSIDE
        holder.itemView.imgTestimonial.setImageResource(R.drawable.video_placeholder)

        if (listOfData[position].image_video != "") {
            holder.itemView.loader.visibility = View.VISIBLE
            Glide.with(context)
                .setDefaultRequestOptions(requestOptions)
                .asBitmap()
                .load(IMAGEBASEURL.plus(listOfData[position].image_video))
                .listener(object : RequestListener<Bitmap> {
                    override fun onLoadFailed(
                        e: GlideException?,
                        model: Any?,
                        target: Target<Bitmap>?,
                        isFirstResource: Boolean
                    ): Boolean {
                        holder.itemView.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_INSIDE
                        holder.itemView.imgTestimonial.setImageResource(R.drawable.video_placeholder)
                        holder.itemView.loader.visibility = View.GONE
                        holder.itemView.imgPlay.visibility = View.GONE
                        return false
                    }

                    override fun onResourceReady(
                        resource: Bitmap?,
                        model: Any?,
                        target: Target<Bitmap>?,
                        dataSource: DataSource?,
                        isFirstResource: Boolean
                    ): Boolean {
                        if (resource != null) {
                            holder.itemView.loader.visibility = View.GONE
                            holder.itemView.imgPlay.visibility = View.VISIBLE
                            holder.itemView.imgTestimonial.scaleType =
                                ImageView.ScaleType.CENTER_CROP
                            holder.itemView.imgTestimonial.setImageBitmap(resource)
                        } else {
                            holder.itemView.loader.visibility = View.GONE
                            holder.itemView.imgTestimonial.scaleType =
                                ImageView.ScaleType.CENTER_INSIDE
                            holder.itemView.imgTestimonial.setImageResource(R.drawable.video_placeholder)
                            holder.itemView.imgPlay.visibility = View.GONE
                        }
                        return true
                    }
                })
                .into(holder.itemView.imgTestimonial)
        } else {
            holder.itemView.loader.visibility = View.GONE
            holder.itemView.imgPlay.visibility = View.GONE
        }

        if (fromCircular) {
            holder.itemView.layout_btn.visibility = View.VISIBLE
            holder.itemView.setOnClickListener {
                if ((listOfData != null) && (listOfData.size > holder.absoluteAdapterPosition) && (holder.absoluteAdapterPosition != -1)) {
                    clickListener.onClickViewCircular(
                        it,
                        listOfData[holder.absoluteAdapterPosition]
                    )
                }
            }

            holder.itemView.relPlay.setOnClickListener {
                if ((listOfData != null) && (listOfData.size > holder.absoluteAdapterPosition) && (holder.absoluteAdapterPosition != -1)) {
                    clickListener.onClickView(it, listOfData[holder.absoluteAdapterPosition])
                }
            }
        } else {
            holder.itemView.layout_btn.visibility =  View.GONE
            holder.itemView.setOnClickListener {
                if ((listOfData != null) && (listOfData.size > holder.absoluteAdapterPosition) && (holder.absoluteAdapterPosition != -1)) {
                    clickListener.onClickView(it, listOfData[holder.absoluteAdapterPosition])
                }
            }

        }
    }

    private fun getInstitutionType(institutionalType: String?): String {
        return when (institutionalType!!) {
            "1" -> "Tea Estate"
            "2" -> "Sugar Mill"
            else -> "Influencer"
        }
    }

    override fun getItemCount(): Int = listOfData.size

    interface OnClickItem {
        fun onClickView(view: View, subMenu: TestimonialListData)
        fun onClickViewCircular(view: View, subMenu: TestimonialListData)
    }

}