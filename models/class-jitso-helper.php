<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Helper' ) ) {

    final class JITSO_Helper {

        /**
		 * Check if current user is authorized to execute an operation within the 'Just In Time Sales Offers' plugin.
		 *
		 * @access public
		 * @since 1.0.0
		 *
		 * @param null $user
		 * @return bool
		 */
		public static function current_user_authorized( $user = null ) {

            $constants = JITSO_Constants::instance();

			$jitso_admin_roles = $constants->ROLES_ALLOWED_TO_MANAGE_JITSO();

			if ( is_null( $user ) )
				$user = wp_get_current_user();

			if ( $user->ID ) {

				if ( count( array_intersect( ( array ) $user->roles , $jitso_admin_roles ) ) )
					return true;
				else
					return false;

			} else
				return false;

		}

        /**
         * It returns an array of Post objects.
         * Get all products of the shop via $wpdb.
         *
         * @since 1.0.0
         * @return mixed
         *
         * @param null $limit
         * @param string $order_by
         * @return mixed
         */
        public static function get_all_products( $limit = null , $order_by = 'DESC' ) {

            global $wpdb;

            $query = "
                      SELECT *
                      FROM $wpdb->posts
                      WHERE post_status = 'publish'
                      AND post_type = 'product'
                      ORDER BY $wpdb->posts.post_date " . $order_by . "
                    ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get all the pages of the current site via wpdb.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $limit
         * @param string $order_by
         * @return mixed
         */
        public static function get_all_site_pages( $limit = null , $order_by = 'DESC' ) {

            global $wpdb;

            $query = "
                      SELECT * FROM $wpdb->posts
                      WHERE $wpdb->posts.post_status = 'publish'
                      AND $wpdb->posts.post_type = 'page'
                      ORDER BY $wpdb->posts.post_date " . $order_by . "
                     ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get the title of the page id based on the page type.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $type
         * @param $id
         * @return mixed
         */
        public static function get_id_title( $type , $id ) {

			$title = "";

            switch ( $type ) {

                case 'page':
                case 'post':
                case 'product':
					$title = get_the_title( $id );
                    break;

                case 'product-category':
					$title = get_cat_name( $id );
                    break;

            }

            return apply_filters( 'jitso_get_id_text' , $title , $type , $id );

        }

        /**
         * Get variable product variations.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $args
         * @return array
         */
        public static function get_product_variations( $args ) {

            if ( isset( $args[ 'product' ] ) )
                $product = $args[ 'product' ];
            elseif ( isset( $args[ 'variable_id' ] ) )
                $product = wc_get_product( $args[ 'variable_id' ] );

			$variation_arr = array();

			if ( $product ) {

				$product_variations = $product->get_available_variations();
				$product_attributes = $product->get_attributes();

				foreach ( $product_variations as $variation ) {

					if ( isset( $args[ 'variation_id' ] ) && $args[ 'variation_id' ] != $variation[ 'variation_id' ] )
						continue;

					$variation_obj            = wc_get_product( $variation[ 'variation_id' ] );
					$variation_attributes     = $variation_obj->get_variation_attributes();
					$friendly_variation_text  = null;
					$variation_attributes_arr = array();

					foreach ( $variation_attributes as $variation_name => $variation_val ) {

						foreach ( $product_attributes as $attribute_key => $attribute_arr ) {

							if ( $variation_name != 'attribute_' . sanitize_title( $attribute_arr[ 'name' ] ) )
								continue;

							$attr_found = false;

							if ( $attribute_arr[ 'is_taxonomy' ] ) {

								// This is a taxonomy attribute
								$variation_taxonomy_attribute = wp_get_post_terms( $product->id , $attribute_arr[ 'name' ] );

								foreach ( $variation_taxonomy_attribute as $var_tax_attr ) {

									if ( $variation_val == $var_tax_attr->slug ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": " . $var_tax_attr->name;
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": " . $var_tax_attr->name;

										$attr_key = "attribute_pa_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) ) );
										$attr_val = $var_tax_attr->slug;

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = $attr_val;
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => $attr_val );

										$attr_found = true;
										break;

									} elseif ( empty( $variation_val ) ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";

										$attr_key = "attribute_pa_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) ) );

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = "any";
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => "any" );

										$attr_found = true;
										break;

									}

								}

							} else {

								// This is not a taxonomy attribute

								$attr_val = explode( '|' , $attribute_arr[ 'value' ] );

								foreach ( $attr_val as $attr ) {

									$attr = trim( $attr );

									// I believe the reason why I wrapped the $attr with sanitize_title is to remove special chars
									// We need ot wrap variation_val too to properly compare them
									if ( sanitize_title( $variation_val ) == sanitize_title( $attr ) ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , $attribute_arr[ 'name' ] ) . ": " . $attr;
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , $attribute_arr[ 'name' ] ) . ": " . $attr;

										$attr_key = "attribute_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , $attribute_arr[ 'name' ] ) ) );

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = $attr;
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => $attr );

										$attr_found = true;
										break;

									} elseif ( empty( $variation_val ) ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";

										$attr_key = "attribute_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , $attribute_arr[ 'name' ] ) ) );

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = "Any";
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => "Any" );

										$attr_found = true;
										break;

									}

								}

							}

							if ( $attr_found )
								break;

						}

					}

					if ( ( $product->managing_stock() === true && $product->get_total_stock() > 0 && $variation_obj->managing_stock() === true && $variation_obj->get_total_stock() > 0 && $variation_obj->is_purchasable() ) ||
						( $product->managing_stock() !== true && $variation_obj->is_in_stock() && $variation_obj->is_purchasable() ) ||
						( $variation_obj->backorders_allowed() && $variation_obj->is_purchasable() ) ) {

						//if ( $variation[ 'is_in_stock' ] && $variation_obj->is_purchasable() ) {
						$variation_arr[] = array(
							'value'      => $variation[ 'variation_id' ],
							'text'       => $friendly_variation_text,
							'disabled'   => false,
							'visible'    => true,
							'attributes' => $variation_attributes_arr
						);

					} else {

						$visibility = false;
						if ( $variation_obj->variation_is_visible() )
							$visibility = true;

						$variation_arr[] = array(
							'value'      => 0,
							'text'       => $friendly_variation_text,
							'disabled'   => true,
							'visible'    => $visibility,
							'attributes' => $variation_attributes_arr
						);

					}

				}

				wp_reset_postdata();

				usort( $variation_arr , array( 'JITSO_Helper' , 'usort_variation_menu_order') ); // Sort variations via menu order

			}

            return $variation_arr;

        }

        /**
         * usort callback that sorts variations based on menu order.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $arr1
         * @param $arr2
         * @return int
         */
        public static function usort_variation_menu_order( $arr1 , $arr2 ) {

            $product1_id = $arr1[ 'value' ];
            $product2_id = $arr2[ 'value' ];

            $product1_menu_order = get_post_field( 'menu_order', $product1_id );
            $product2_menu_order = get_post_field( 'menu_order', $product2_id );

            if ( $product1_menu_order == $product2_menu_order )
                return 0;

            return ( $product1_menu_order < $product2_menu_order ) ? -1 : 1;

        }

        /**
         * It returns an array of Post objects.
         * Get all coupons of the shop via $wpdb.
         *
         *
         * @param null $limit
         * @param string $order_by
         * @return mixed
         */
        public static function get_all_coupons( $limit = null , $order_by = 'DESC' ) {

            global $wpdb;

            $query = "
                      SELECT *
                      FROM $wpdb->posts
                      WHERE post_status = 'publish'
                      AND post_type = 'shop_coupon'
                      ORDER BY $wpdb->posts.post_date " . $order_by . "
                    ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

		/**
		 * Get info about a given coupon.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param $coupon_id Id of the coupon ( not the coupon code, the numeric id )
		 * @return array|WP_Error Array of coupon information if success, WP_Error on failure
		 */
        public static function get_coupon_info( $coupon_id ) {

			if ( is_string( get_post_status( $coupon_id ) ) && get_post_type( $coupon_id ) == 'shop_coupon' ) {

				$coupon_code     = get_the_title( $coupon_id );
				$coupon_obj      = new WC_Coupon( $coupon_code );
				$coupon_types    = wc_get_coupon_types();
                $coupon_obj_type = self::get_coupon_data( $coupon_obj , 'discount_type' );

				return array(
						'coupon_url'       => home_url( "/wp-admin/post.php?post=" . $coupon_id . "&action=edit" ),
						'coupon_type_text' => isset( $coupon_types[ $coupon_obj_type ] ) ? $coupon_types[ $coupon_obj_type ] : '',
						'coupon_obj'	   => $coupon_obj
				);

			} else
				return new WP_Error( "Invalid Coupon Id" , __( "Coupon Id supplied is invalid or does not exist" , "just-in-time-sales-offers" ) );

        }

        /**
         * Get client ip.
         *
         * @since 1.1.3
         * @access public
         *
         * @return string;
         */
        public static function get_client_ip() {

            if ( !empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {

                $ips = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
                $ips = explode( ',' , $ips );
                $ips = array_map( 'trim' , $ips );
                $ip  = array_pop( $ips );

            } else
                $ip = $_SERVER['REMOTE_ADDR'];

            return $ip;

        }

		/**
		 * Get all jit sales offers.
		 *
		 * @since 1.2.0
		 * @access public
		 *
		 * @param null $limit
		 * @param string $order_by
		 * @return mixed
		 */
		public static function get_all_jit_sales_offers( $limit = null , $order_by = 'DESC' ) {

			global $wpdb;

			$constants = JITSO_Constants::instance();

			$query = "
                      SELECT * FROM $wpdb->posts
                      WHERE $wpdb->posts.post_status = 'publish'
                      AND $wpdb->posts.post_type = '" . $constants->OFFER_CPT_NAME() . "'
                      ORDER BY $wpdb->posts.post_date " . $order_by . "
                     ";

			if ( $limit && is_numeric( $limit ) )
				$query .= " LIMIT " . $limit;

			return $wpdb->get_results( $query );

		}

        /**
         * Get variation data by attributes.
         *
         * @since 1.2.0
         * @access public
         *
         * @param int   $variable_product_id Variable product id.
         * @param array $selected_attributes Selected variable product attributes.
         * @return int Variation id
         */
        public static function get_variation_data_by_attributes( $variable_product_id , $selected_attributes ) {

            $product = wc_get_product( $variable_product_id );

            if ( $product->product_type == 'variable' ) {

                $product_variations = $product->get_available_variations();
                $product_attributes = $product->get_attributes();
                $varaition_data     = array();

                foreach( $product_variations as $variation ) {

                    $variation_hit                  = true;
                    $varaition_data[ 'attributes' ] = array();

                    foreach ( $selected_attributes as $selected_attribute => $selected_attribute_value ) {

                        if ( !array_key_exists( 'attribute_' . $selected_attribute , $variation[ 'attributes' ] ) ||
                              strcasecmp( $variation[ 'attributes' ][ 'attribute_' . $selected_attribute ] , $selected_attribute_value ) !== 0 ) {

                            $variation_hit = false;
                            break;

                        }

                        $variation_data[ 'attributes' ][ $selected_attribute ] = $variation[ 'attributes' ][ 'attribute_' . $selected_attribute ];

                    }

                    if ( $variation_hit ) {

                        $variation_data[ 'variation_id' ] = $variation[ 'variation_id' ];
                        break;

                    }

                }

                return $variation_data;

            } else
                return false; // Not a variable product

        }

	    /**
		 * Returns the timezone string for a site, even if it's set to a UTC offset
		 *
		 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
		 *
		 * Reference:
		 * http://www.skyverge.com/blog/down-the-rabbit-hole-wordpress-and-timezones/
		 *
		 * @since 1.2.0
		 * @access public
		 *
		 * @return string valid PHP timezone string
		 */
		public static function get_site_current_timezone() {

			// if site timezone string exists, return it
			if ( $timezone = get_option( 'timezone_string' ) )
				return $timezone;

			// get UTC offset, if it isn't set then return UTC
			if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) )
				return 'UTC';

			return self::convert_utc_offset_to_timezone( $utc_offset );

		}

		/**
		 * Conver UTC offset to timezone.
		 *
		 * @since 1.2.0
		 * @access public
		 *
		 * @param $utc_offset float/int/sting UTC offset.
		 * @return string valid PHP timezone string
		 */
		public static function convert_utc_offset_to_timezone( $utc_offset ) {

			// adjust UTC offset from hours to seconds
			$utc_offset *= 3600;

			// attempt to guess the timezone string from the UTC offset
			if ( $timezone = timezone_name_from_abbr( '' , $utc_offset , 0 ) )
				return $timezone;

			// last try, guess timezone string manually
			$is_dst = date( 'I' );

			foreach ( timezone_abbreviations_list() as $abbr ) {

				foreach ( $abbr as $city ) {

					if ( $city[ 'dst' ] == $is_dst && $city[ 'offset' ] == $utc_offset )
						return $city[ 'timezone_id' ];

				}

			}

			// fallback to UTC
			return 'UTC';

		}

        /**
         * Get data about the current woocommerce installation.
         *
         * @since 1.2.6
         * @access public
         * @return array Array of data about the current woocommerce installation.
         */
        public static function get_woocommerce_data() {

            if ( ! function_exists( 'get_plugin_data' ) )
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

            return get_plugin_data( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' );

        }

        /**
         * Get order properties based on the key. WC 2.7
         *
         * @since 1.2.6
         * @access public
         *
         * @param WC_Order $order  order object
         * @param string   $key    order property
         * @return string   order property
         */
        public static function get_order_data( $order , $key ) {

            if ( is_a( $order , 'WC_Order' ) ) {

                $woocommerce_data = self::get_woocommerce_data();

                if ( version_compare( $woocommerce_data[ 'Version' ] , '3.0.0' , '>=' ) ) {

                    switch ( $key ) {

                        case 'modified_date' :
                            $order_date_modified = $order->get_date_modified();
                            return $order_date_modified->date( 'Y-m-d H:i:s' );
                            break;

                        default:
                            $key = 'get_' . $key;
                            return $order->$key();
                            break;
                    }

                }
                else
                    return $order->$key;

            } else {

                error_log( 'JITSO Error : get_order_data helper functions expect parameter $order of type WC_Order.' );
                return 0;

            }
        }

        /**
         * Get coupon properties based on the key. WC 2.7
         *
         * @since 1.2.6
         * @access public
         *
         * @param WC_Coupon $coupon  coupon object
         * @param string   $key    coupon property
         * @return string   coupon property
         */
        public static function get_coupon_data( $coupon , $key ) {

            if ( is_a( $coupon , 'WC_Coupon' ) ) {

                $woocommerce_data = self::get_woocommerce_data();

                if ( version_compare( $woocommerce_data[ 'Version' ] , '3.0.0' , '>=' ) ) {

                    switch ( $key ) {

                        case 'coupon_amount' :
                            return $coupon->get_amount();
                            break;

                        default:
                            $key = 'get_' . $key;
                            return $coupon->$key();
                            break;
                    }

                } else
                    return $coupon->$key;

            } else {

                error_log( 'JITSO Error : get_coupon_data helper functions expect parameter $order of type WC_Coupon.' );
                return 0;

            }
        }

    }

}
