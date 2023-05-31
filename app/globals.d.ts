declare global {
  namespace NodeJS {
    interface ProcessEnv {
      NEXT_PUBLIC_SITE_URL: string;
      NEXT_PUBLIC_RECAPTCHA_SITE_KEY: string;
      RECAPTCHA_SECRET_KEY: string;
      NEXT_PUBLIC_GTM_ID: string;
      MAILGUN_API_KEY: string;
      MAILGUN_DOMAIN: string;
      MAILGUN_HOST: string;
      MAILGUN_EMAIL_FROM: string;
      MAILGUN_EMAIL_TO: string;
      PLACES_API_KEY: string;
      NEXT_PUBLIC_PLACES_PLACE_ID: string;
      NEXT_PUBLIC_WP_GRAPHQL_URL: string;
      WP_AUTH_REFRESH_TOKEN: string;
      WP_PREVIEW_SECRET: string;
      ISR_REVALIDATE_SECRET: string;
    }
  }
  interface Window {
    dataLayer: Record<string, unknown>[];
    grecaptcha: {
      /**
       * Programatically invoke the reCAPTCHA check. Used if the invisible reCAPTCHA is on a div
       * instead of a button.
       *
       * @param {string} opt_widget_id Optional widget ID, defaults to the first widget created if
       *     unspecified.
       */
      execute(opt_widget_id?: string): void;

      /**
       * Renders the container as a reCAPTCHA widget and returns the ID of the newly created widget.
       *
       * @param {ElementRef|string} container The HTML element to render the reCAPTCHA widget.  Specify
       *    either the ID of the container (string) or the DOM element itself.
       * @param {Object} parameters An object containing parameters as key=value pairs, for example,
       *    {"sitekey": "your_site_key", "theme": "light"}.
       */
      render(
        container: ElementRef | string,
        parameters: { [key: string]: string }
      ): void;

      /**
       * Resets the reCAPTCHA widget.
       *
       * @param {string} opt_widget_id Optional widget ID, defaults to the first widget created if
       *     unspecified.
       */
      reset(opt_widget_id?: string): void;

      /**
       * Gets the response for the reCAPTCHA widget. Returns a null if reCaptcha is not validated.
       *
       * @param {string} opt_widget_id Optional widget ID, defaults to the first widget created if
       *     unspecified.
       */
      getResponse(opt_widget_id?: string): string;
    };
  }
}

export {};
